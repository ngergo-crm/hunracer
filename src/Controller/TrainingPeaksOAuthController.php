<?php


namespace App\Controller;


use App\Entity\Workouts;
use App\Services\TrainingPeaksService;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Utils\Json;
use phpDocumentor\Reflection\Types\Mixed_;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#OAuth docs: https://github.com/TrainingPeaks/PartnersAPI/wiki/OAuth

/**
 * @IsGranted("IS_AUTHENTICATED_FULLY")
 */
class TrainingPeaksOAuthController extends AbstractController
{
    #CLIENT CONSTANTS
    private const CLIENT_ID = "webfish-kft";
    private const CLIENT_SECRET = "5yEvVd8qt1nAwyWhghoeEEfidpIWNVytd5sWmVC9v0";
    private const CLIENT_SCOPE = "events:read metrics:read workouts:details workouts:read";
    private const CLIENT_SCOPE_ARRAY = ["events:read", "metrics:read", "workouts:details", "workouts:read"];
    public const COOKIE_TOKEN_NAME = 'tpToken';

    #TP OAUTH CONSTANTS TEST
    public const AUTHORIZE_URL_TEST = "https://oauth.sandbox.trainingpeaks.com/OAuth/Authorize";
    public const TOKENEXCHANGE_URL_TEST = "https://oauth.sandbox.trainingpeaks.com/oauth/token";
    public const DEAUTHORIZE_URL_TEST = "https://oauth.sandbox.trainingpeaks.com/oauth/deauthorize";
    public const API_HOST_TEST = "https://api.sandbox.trainingpeaks.com/";


    #TP OAUTH CONSTANTS PROD
    public const AUTHORIZE_URL = "https://oauth.trainingpeaks.com/OAuth/Authorize";
    public const TOKENEXCHANGE_URL = "https://oauth.trainingpeaks.com/oauth/token";
    public const DEAUTHORIZE_URL = "https://oauth.trainingpeaks.com/oauth/deauthorize";
    public const API_HOST = "https://api.trainingpeaks.com/";

    private $test;
    private $session;
    private $tpService;

    public function __construct(SessionInterface $session, ParameterBagInterface $bag, TrainingPeaksService $tpService)
    {
        $this->session = $session;
        $this->test = $bag->get('APP_ENV') === 'dev';
        $this->tpService = $tpService;
    }

    /**
     * @Route("/getTPLoginUrl", name="getTPLoginUrl", methods={"POST"}, options={"expose"=true})
     * @param Request $request
     * @return JsonResponse
     */
    public function returnTrainingPeaksLoginUrl(Request $request): JsonResponse
    {
        $test = json_decode($request->request->get('test', false), true);
        $url = !$test ? self::AUTHORIZE_URL : self::AUTHORIZE_URL_TEST;
        $responseType = "code";
        $redirectUri = urlencode($this->getRedirectUri());
        $url = sprintf("%s?response_type=%s&client_id=%s&scope=%s&redirect_uri=%s", $url, $responseType, self::CLIENT_ID, urlencode(self::CLIENT_SCOPE), $redirectUri);
        return new JsonResponse([
            'tpLoginUrl' => $url,
        ]);
    }

    //TP
    //ngergo
    //123456
    //To create a new athlete, please go to https://home.sandbox.trainingpeaks.com/signup?partner=webfish-kft
    //To create a new coach, please go to https://home.sandbox.trainingpeaks.com/coach/signup?partner=webfish-kft
    //nggergo
    //123456
    //efEgdRvrít

    /**
     * Ezt a TP autorization-höz használjuk. Itt kapjuk meg a refresh tokent.
     * @Route("/tpCallback", name="tpCallback")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function trainingPeaksCallback(Request $request): RedirectResponse
    {
        $response = new RedirectResponse($this->generateUrl("home"));
        $code = $request->query->get('code');
        $route = $request->getPathInfo();
        if ($code and $route === $this->generateUrl('tpCallback')) {
            $grantType = "authorization_code";
            $redirectUri = $this->getRedirectUri();
            $tokenExchangeUrl = !$this->test ? self::TOKENEXCHANGE_URL : self::TOKENEXCHANGE_URL_TEST;
            $postfield = sprintf('client_id=%s&grant_type=%s&code=%s&redirect_uri=%s&client_secret=%s', self::CLIENT_ID, $grantType, $code, $redirectUri, self::CLIENT_SECRET);
            $token = $this->handleTpRequest($tokenExchangeUrl, $postfield);
            $token = $this->setTokenExpiresAt($token);
            //todo aszinkron kulccsal bekódolni a tokent és úgy mentjük bele a cookie-ba!!! Mindig kikódoljuk használat esetén!
            $response->headers->setCookie($this->createTpCookie($token));
            //$response->send();
        }
        return $response;
    }

    /**
     * @Route("/tpApiRequest", name="tpApiRequest", options={"expose"=true}, methods={"POST"})
     */
    public function makeTpApiRequest(Request $request): JsonResponse
    {
        $jsonResponse = new JsonResponse();
        $endpoint = $request->request->get('endpoint');
        $token = json_decode($request->cookies->get(self::COOKIE_TOKEN_NAME), true);
        $response = $this->tpService->apiRequest($endpoint, $token, $this->test);
        if ($response['token'] !== $token) {
            $jsonResponse->headers->setCookie($this->createTpCookie($response['token']));
        }
        $jsonResponse->setData(["response" => $response['response'], "token" => $token]);
        return $jsonResponse;
    }

    /**
     * @Route("/tpDeauthorize", name="tpDeauthorization", methods={"POST"}, options={"expose"=true})
     * @param Request $request
     * @return JsonResponse
     */
    public function trainingPeaksDeauthorization(Request $request): JsonResponse
    {
        $token = json_decode($request->cookies->get(self::COOKIE_TOKEN_NAME), true);
        $isRefreshTokenNeeded = \DateTime::__set_state($token['expires_at']) < new \DateTime('now');
        if ($token) {
            $refreshSuccessful = false;
            if ($isRefreshTokenNeeded) {
                //try to refresh token
                $token = $this->refreshTpToken($token);
                $refreshSuccessful = $token ? true : false;
            }
            if (!$isRefreshTokenNeeded or $refreshSuccessful) {
                //unauth on TP's side if we have a valide token
                $endpoint = !$this->test ? self::DEAUTHORIZE_URL : self::DEAUTHORIZE_URL_TEST;
                $this->handleDeauth($endpoint, $token['access_token']);
            }
            //remove cookie anyway
            $response = new Response();
            $response->headers->clearCookie($this->getParameter("cookieName"));
            $response->send();
            $request->cookies->remove(self::COOKIE_TOKEN_NAME);
        }
        return new JsonResponse([
            "tokenAvailable" => false
        ], 200);
    }

    /**
     * @Route("/startImport", name="startImport", methods={"POST"}, options={"expose"=true})
     * @param Request $request
     * @return JsonResponse
     */
    public function startImport(ParameterBagInterface $parameterBag, Request $request): JsonResponse
    {
        $token = $request->cookies->get(self::COOKIE_TOKEN_NAME);
        $processes = ['workouts:last-year'];
        $processIndex = 1;
        $root = $parameterBag->get('kernel.project_dir');
        $phpexec = (new PhpExecutableFinder)->find();
        foreach ($processes as $process) {
            ${'process' . $processIndex} = new Process([$phpexec, $root . '/bin/console', $process, $token, $this->getUser()->getId()]);
            ${'process' . $processIndex}->setTimeout(null);
            ${'process' . $processIndex}->start();
            ++$processIndex;
        }
        for ($processNumber = 1; $processNumber < $processIndex; $processNumber++) {
            ${'process' . $processNumber}->wait(
                function ($type, $buffer) {
                    if (Process::ERR === $type) {
                        dd('ERR > ' . $buffer);
                    } else {
                        dd('OUT > ' . $buffer);
                    }
                }
            );
        }

        return new JsonResponse([
            'test' => 'ok',
        ]);
    }

    /**
     * @Route("/workout_refresh", name="workout_refresh", methods={"POST"})
     */
    public function workoutQuickRefresh(Request $request, EntityManagerInterface $manager)
    {
        $jsonResponse = new JsonResponse();
        $workoutRepo = $manager->getRepository(Workouts::class);
        $token = json_decode($request->cookies->get(self::COOKIE_TOKEN_NAME), true);

        $autoRefresh = $request->request->get('autoRefresh', false);
        $startDate = $request->request->get('start');
        $endDate = $request->request->get('end');
        $detailedRefresh = ($startDate and $endDate);
        if (!$detailedRefresh) {
            $startDate = date("Y-m-d", strtotime('now', strtotime(date('Y-m-d'))));
            $endDate = date("Y-m-d", strtotime($workoutRepo->getLastInsertedWorkoutTime($this->getUser()), strtotime(date('Y-m-d'))));
        }
        $sameDay = $startDate === $endDate; //ez mindig az aznapit nézi csak

        $periods = $this->getWorkoutPeriods($startDate, $endDate);
        $test = [];
        foreach ($periods as $period) {
            $endpoint = sprintf('v1/workouts/%s/%s', $period['end'], $period['start']);
            $existingWorkouts = $workoutRepo->getWorkoutDays($this->getUser(), $period, false);
            $response = $this->tpService->apiRequest($endpoint, $token, $this->test);
            $workouts = $response['response'];
            $test[] = $workouts;
            $responseToken = $response['token'];
            if ($responseToken !== $token) {
                $jsonResponse->headers->setCookie($this->tpService->createTpCookie($responseToken));
            }
            if (isset($workouts) and is_array($workouts)) {
                foreach ($workouts as $workoutData) {
                    $isExist = false;
                    $checkIfExists = false;
                    if ($sameDay or $workoutData['WorkoutDay'] === $endDate or $detailedRefresh) {
                        $checkIfExists = true;
                    }
                    if ($checkIfExists and $existingWorkouts) {
                        $isExist = $this->checkWorkoutExistence($existingWorkouts, $workoutData);
                    }
                    if (!$isExist and $workoutData['Completed']) {
                        $this->insertWorkout($manager, $workoutData);
                    } else if ($detailedRefresh and $isExist) {
                        $this->updateWorkout($manager, $isExist, $workoutData);
                    }

                }
                $this->deleteOrphanedWorkouts($manager, $existingWorkouts, $workouts);
            }
        }
        if ($autoRefresh) {
            $request->getSession()->set('autoTpQuickRefresh', false);
        }
        $jsonResponse->setData($test);
        return $jsonResponse;
    }

    private function getWorkoutPeriods($absoluteStartDate, $absoluteEndDate): array
    {
        $lastPeriodEndDate = date("Y-m-d", strtotime('-45 days', strtotime($absoluteStartDate)));
        $largePeriod = $absoluteEndDate < $lastPeriodEndDate;
        $intervals = [];
        if ($largePeriod) {
            $intervals[] = ['start' => $absoluteStartDate, 'end' => $lastPeriodEndDate];
            while ($absoluteEndDate < $lastPeriodEndDate) {
                $lastPeriodStart = date("Y-m-d", strtotime('-1 day', strtotime($lastPeriodEndDate)));
                $lastPeriodEndDate = date("Y-m-d", strtotime('-45 days', strtotime($lastPeriodStart)));
                $lastPeriodEndDate = $lastPeriodEndDate < $absoluteEndDate ? $absoluteEndDate : $lastPeriodEndDate;
                $intervals[] = ['start' => $lastPeriodStart, 'end' => $lastPeriodEndDate];
            }
        } else {
            $intervals[] = ['start' => $absoluteStartDate, 'end' => $absoluteEndDate];
        }
        return $intervals;
    }

    /**
     * @return string
     */
    private
    function getRedirectUri(): string
    {
        return $this->generateUrl("tpCallback", [], UrlGeneratorInterface::ABSOLUTE_URL);
    }

    /**
     * @param $token
     * @return Cookie
     */
    private
    function createTpCookie(array $token): Cookie
    {
        return $cookie = new Cookie(
            self::COOKIE_TOKEN_NAME,
            json_encode($token, true),
            time() + (2 * 365 * 24 * 60 * 60)  // Expires 2 years.
        );
    }

    private
    function refreshTpToken(array $token)
    {
        $tokenExchangeUrl = !$this->test ? self::TOKENEXCHANGE_URL : self::TOKENEXCHANGE_URL_TEST;
        $grantType = 'refresh_token';
        $refreshToken = $token['refresh_token'];
        $postfield = sprintf('client_id=%s&grant_type=%s&refresh_token=%s&client_secret=%s', self::CLIENT_ID, $grantType, $refreshToken, self::CLIENT_SECRET);
        $token = $this->handleTpRequest($tokenExchangeUrl, $postfield);
        $token = !isset($token['error']) ? $this->setTokenExpiresAt($token) : null;
        return $token;
    }

    private
    function handleTpApiRequest(string $endpoint, string $token)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer $token"
            ),
        ));

        $response = curl_exec($curl);
        if ($response === FALSE) {
            die(curl_error($curl));
        }
        curl_close($curl);
        return $response = json_decode($response, true);
    }

    /**
     * @param string $endpoint
     * @param string|null $postfields
     * @param array $header
     */
    private
    function handleTpRequest(string $endpoint, ?string $postfields, array $header = [])
    {
        $postfields = !$postfields ? "" : $postfields;
        $curl = curl_init();
        $parameters = array(
            CURLOPT_URL => $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $postfields,
            CURLOPT_HEADER => $header
        );
        curl_setopt_array($curl, $parameters);

        $response = curl_exec($curl);
        if ($response === FALSE) {
            die(curl_error($curl));
        }
        curl_close($curl);
        return $response = json_decode($response, true);
    }

    private
    function handleDeauth(string $endpoint, string $token)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => "",
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer $token"
            ),
        ));
        curl_exec($curl);
        curl_close($curl);
    }

    private
    function makeApiRequestHeader(array $token): array
    {
        $accessToken = $token['access_token'];
        return array(
            "Authorization: Bearer $accessToken",
        );
    }


    /**
     * @param array $token
     * @return array
     */
    private
    function setTokenExpiresAt(?array $token)
    {
        if ($token) {
            $token['expires_at'] = new \DateTime(sprintf('now + %d seconds', $token['expires_in']));
        }
        return $token;
    }

    private function insertWorkout(EntityManagerInterface $manager, array $workoutData): void
    {
        /** @var Workouts $workout */
        $workout = $this->constructWorkout($workoutData);
        $manager->persist($workout);
        $manager->flush();
    }


    private function updateWorkout(EntityManagerInterface $manager, Workouts $existingWorkout, $workoutData): void
    {
        if ($existingWorkout->getData()['LastModifiedDate'] < $workoutData['LastModifiedDate']) {
            $workout = $this->constructWorkout($workoutData, $existingWorkout);
            $manager->persist($workout);
            $manager->flush();
        }
    }

    private function constructWorkout(array $workoutData, $existingWorkout = null): Workouts
    {
        $workoutId = $workoutData['Id'];
        $workoutDay = $workoutData['WorkoutDay'];
        $distance = $workoutData['Distance'];
        $totalTime = $workoutData['TotalTime'];
        $energy = $workoutData['Energy'];
        $tssActual = $workoutData['TssActual'];
        $elevationGain = $workoutData['ElevationGain'];
        $workoutType = $workoutData['WorkoutType'];
        $workout = $existingWorkout ? $existingWorkout : new Workouts();
        $workout->setUser($this->getUser())
            ->setWorkoutId($workoutId)
            ->setWorkoutDay(new \DateTime(date("Y-m-d", strtotime($workoutDay))))
            ->setEnergy($energy)
            ->setTotalTime($totalTime)
            ->setDistance($distance)
            ->setData($workoutData)
            ->setTss($tssActual)
            ->setElevation($elevationGain)
            ->setType($workoutType);
        return $workout;
    }

    private function checkWorkoutExistence(array $existingWorkouts, array $workoutData)
    {
        $isExist = false;
        $index = 0;
        $N = count($existingWorkouts);
        while ($index < $N && !($existingWorkouts[$index]->getWorkoutId() == $workoutData['Id'])) {
            $index++;
        }
        if ($index < $N) {
            $isExist = $existingWorkouts[$index];
        }
        return $isExist;
    }

    private function deleteOrphanedWorkouts(EntityManagerInterface $manager, array  $existingWorkouts, array $newWorkouts):void
    {
        /** @var Workouts $workout */
        foreach ($existingWorkouts as $workout)
        {
            if($newWorkouts) {
                $index = 0;
                $N = count($newWorkouts);
                while ($index < $N && !($workout->getWorkoutId() == $newWorkouts[$index]['Id'])) {
                    $index++;
                }
                $delete = $index >= $N;
            } else {
                $delete = true;
            }
            if($delete) {
                $manager->remove($workout);
                $manager->flush();
            }
        }
    }
}
