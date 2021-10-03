<?php

namespace App\Controller;

use App\Entity\Config;
use App\Entity\MetricRecord;
use App\Entity\MetricType;
use App\Entity\User;
use App\Entity\Workouts;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("IS_AUTHENTICATED_FULLY")
 */
class AccountController extends AbstractController
{
    /**
     * @Route("/profile", name="account")
     */
    public function index(): Response
    {
        $workoutYearStart = $this->getDoctrine()->getRepository(Config::class)->findOneBy(["settingKey" => 'workoutYearStart']);
        return $this->render('account/account.html.twig', [
            'workoutYearStart' => $workoutYearStart->getSettingValue()
        ]);
    }

    /**
     * @Route("/adatlap/{uuid}", name="athleteDataSheet")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_TRAINER')")
     */
    public function athleteDataSheet(string $uuid): Response
    {
        /** @var User $athlete */
        $athlete = $this->getDoctrine()->getRepository(User::class)->findOneBy(['uuid' => $uuid]);
        if (!$athlete or (($this->isGranted('ROLE_TRAINER') and !$this->isGranted('ROLE_ADMIN')) and $this->getUser()->getTrainerCode() !== $athlete->getTrainerCode())) {
            return $this->redirectToRoute('home');
        }
//        dd($athlete);
        return $this->render('account/athleteDataSheet.html.twig', [
            'athlete' => $athlete
        ]);
    }

    /**
     * @Route("/saveDatasheet", name="saveDatasheet", methods={"POST"})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_TRAINER')")
     */
    public function saveDatasheet(Request $request): Response
    {
        $date = new \DateTime($request->request->get('date'));
        $athleteGuid = $request->request->get('athlete');
        $metrics = json_decode($request->request->get('metrics'), true);

        $access = false;
        /** @var User $athlete */
        $athlete = $this->getDoctrine()->getRepository(User::class)->findOneBy(['uuid' => $athleteGuid]);
        if ($athlete and ($this->isGranted('ROLE_TRAINER') and !$this->isGranted('ROLE_ADMIN'))) {
            $access = $athlete->getTrainerCode() === $this->getUser()->getTrainerCode();
        } else if ($athlete and $this->isGranted('ROLE_TRAINER')) {
            $access = true;
        }
        if ($access and $date and $metrics) {
            $entityManager = $this->getDoctrine()->getManager();
            $recordRepo = $this->getDoctrine()->getRepository(MetricRecord::class);
            $metricTypes = $this->getDoctrine()->getRepository(MetricType::class)->findAll();
            $metricRecords = $recordRepo->findBy(['user'=>$athlete, 'metricCreatedAt' => $date]);

            foreach ($metrics as $field => $metric) {
                //table (X and Y)
                if($field === 'tableRecords') {
                    foreach ($metric as $tablefields) {
                        $fieldX = $tablefields['description'];
                        foreach ($tablefields as $tablefield => $metric) {
                            if($tablefield !== 'description') {
                                $fieldY = $tablefield;
                                if(is_numeric($metric)) {
                                    $metricUpdate = null;
                                    if(count($metricRecords) > 0) {
                                        $metricUpdate = $this->checkMetricExists($metricRecords, $fieldX, $fieldY);
                                    }
                                    if(is_object($metricUpdate)) {
                                        //update
                                        if($metricUpdate->getData() !== $metric) {
                                            $metricUpdate->setData($metric);
                                            $entityManager->persist($metricUpdate);
                                        }
                                    } else {
                                        //insert
                                        $record = new MetricRecord();
                                        $record->setData($metric)
                                            ->setMetricCreatedAt($date)
                                            ->setUser($athlete)
                                            ->setAxisXType($this->getMetricType($metricTypes, $fieldX))
                                            ->setAxisYType($this->getMetricType($metricTypes, $fieldY))
                                        ;
                                        $entityManager->persist($record);
                                    }
                                }
                            }
                        }
                    }
                } else {
                    //generic field (only X)
                    if(is_numeric($metric)) {
                        $metricUpdate = null;
                        if(count($metricRecords) > 0) {
                            $metricUpdate = $this->checkMetricExists($metricRecords, $field);
                        }
                        if(is_object($metricUpdate)) {
                            //update
                            if($metricUpdate->getData() !== $metric) {
                                $metricUpdate->setData($metric);
                                $entityManager->persist($metricUpdate);
                            }
                        } else {
                            //insert
                            $record = new MetricRecord();
                            $record->setData($metric)
                                ->setMetricCreatedAt($date)
                                ->setUser($athlete)
                                ->setAxisXType($this->getMetricType($metricTypes, $field))
                                ->setAxisYType(null)
                            ;
                            $entityManager->persist($record);
                        }
                    }
                }
            }
            $entityManager->flush();
        }
        return new JsonResponse();
    }

    /**
     * @Route("/removeAccountPhoto", name="removeAccountPhoto", methods={"PUT"})
     */
    public function deleteAccountPhoto()
    {
        //todo ezt át lehetne szervezni a user entity-be mint pl. a changePassword!
        $path = "../public/images/accountPhoto/"; //todo global var somehow
        $pathToFile = $path . $this->getUser()->getPhoto();
        unlink($pathToFile);
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['id' => $this->getUser()->getId()]);
        $user->setPhoto(null);
        $manager = $this->getDoctrine()->getManager();
        $manager->persist($user);
        $manager->flush();
        return new Response('', 200);
    }

    /**
     * @Route("/removeMetricRecords", name="removeMetricRecords", methods={"POST"})
     * @Security("is_granted('ROLE_TRAINER')")
     */
    public function removeMetricRecords(Request $request)
    {
        dd('OK');
        $metricRecordIds = json_decode($request->request->get('metricRecords'), true);
//        $userUuid = $request->request->get('user');
//        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['uuid' => $userUuid]);
        //todo + trainer - van-e hozzáférése a user-hez?
        $records = $this->getDoctrine()->getRepository(MetricRecord::class)->findBy(['id' => $metricRecordIds]);
        $manager = $this->getDoctrine()->getManager();
        foreach ($records as $record) {
            $manager->remove($record);
        }
        $manager->flush();
        return new Response('', 204);
    }

    /**
     * @Route("/adat-osszehasonlitas", name="metricsCompare")
     */
    public function metricsCompare(): Response
    {
        $workoutYearStart = $this->getDoctrine()->getRepository(Config::class)->findOneBy(["settingKey" => 'workoutYearStart']);
        return $this->render('admin/metricsCompare.html.twig', [
            'workoutYearStart' => $workoutYearStart->getSettingValue(),
            'user' => $this->getUser()
        ]);
    }

    /**
     * @Route("/getMetricDatasheetDates", name="metricDatasheetDates", methods={"POST"})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_TRAINER')")
     */
    public function getMetricDatasheetDates(Request $request)
    {
        //todo make API route // load with init page
        $athlete = $request->request->get('athlete');
        $data = $this->getDoctrine()->getRepository(MetricRecord::class)->getAthleteDatasheetDates($athlete);
        $formattedData = [];
        foreach ($data as $item) {
            $formattedData[] = $item['metricCreatedAt'];
        }
        //  dd($formattedData);
        return new JsonResponse(
            $formattedData
        );
    }

//    /**
//     * @Route("/getAthleteMetrics", name="getAthleteMetrics", methods={"POST"}, options={"expose"=true})
//     * @param Request $request
//     * @return JsonResponse
//     */
//    public function getAthleteMetrics(Request $request)
//    {
//        $userIds = json_decode($request->request->get('athleteIds'), true);
//        $data = $this->getDoctrine()->getRepository(MetricRecord::class)->getAthleteMetrics($userIds);
//        dd($data);
//        //$data = $this->addNoData($data, $userIds);
//        return new JsonResponse(
//            $data
//        );
//    }
    private function checkMetricExists($metricRecords, $metricX, $metricY = null): ?MetricRecord
    {
        $i = 0;
        $N = count($metricRecords);
        if(!$metricY) {
            while($i < $N and !($metricX === $metricRecords[$i]->getAxisXType()->getName())) {
                $i++;
            }
        } else {
            while($i < $N and !($metricX === $metricRecords[$i]->getAxisXType()->getName() and $metricY === $metricRecords[$i]->getAxisYType()->getName())) {
                $i++;
            }
        }

        return $i < $N? $metricRecords[$i] : null;
    }

    private function getMetricType(array $metricTypes, string $field): ?MetricType
    {
        $i = 0;
        $N = count($metricTypes);
        while($i < $N and !($field === $metricTypes[$i]->getName())) {
            $i++;
        }
        return $i < $N? $metricTypes[$i] : null;
    }
}
