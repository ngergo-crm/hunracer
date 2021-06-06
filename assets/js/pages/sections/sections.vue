<template>
    <div class="contentcentral">
        <div class="contentcard">
            <div class="cardtitle">
                <h4>
                    Szakágak
                </h4>
            </div>
            <div class="newSection">
                <vs-button
                    border
                    @click="active3=!active3"
                >
                    + új szakág
                </vs-button>
                <vs-dialog
                    v-model="active3"
                    width="300px"
                    not-center
                    @close="newSection=''"
                >
                    <template #header>
                        <h4 class="not-margin">
                            Az új szakág megnevezése
                        </h4>
                    </template>

                    <div class="con-content">
                        <vs-input
                            v-model="newSection"
                            style="width: 100%"
                            placeholder="szakág"
                        />
                    </div>

                    <template #footer>
                        <div class="con-footer">
                            <vs-button
                                transparent
                                @click="createSection"
                            >
                                Mentés
                            </vs-button>
                            <vs-button
                                dark
                                transparent
                                @click="active3=false;newSection=''"
                            >
                                Mégsem
                            </vs-button>
                        </div>
                    </template>
                </vs-dialog>
            </div>
            <div>
                <spinner-component v-if="loading" />
                <vs-table
                    v-else
                    class="test"
                    striped
                >
                    <template #thead>
                        <vs-tr>
                            <vs-th>
                                Szakág megnevezése
                            </vs-th>
                            <vs-th />
                        </vs-tr>
                    </template>
                    <template
                        #tbody
                    >
                        <vs-tr
                            v-for="(tr, i) in sections"
                            :key="tr['@id']"
                            :data="tr"
                        >
                            <vs-td
                                edit
                                @click="edit = tr, editProp = 'description', editActive = true, rename = tr.description"
                            >
                                {{ tr.description }}
                            </vs-td>
                            <vs-td>
                                <vs-button
                                    border
                                    danger
                                    @click="deleteSection(tr)"
                                >
                                    Törlés
                                </vs-button>
                            </vs-td>
                        </vs-tr>
                    </template>
                    <template #notFound>
                        <i>
                            Még nincs szakág regisztrálva...
                        </i>
                    </template>
                </vs-table>
                <vs-dialog v-model="editActive">
                    <template #header>
                        Szakág átnevezése
                    </template>
                    <vs-input
                        v-if="editProp === 'description'"
                        v-model="rename"
                        @keypress.enter="editActive = false"
                        @change="renameSection(rename)"
                    />
                </vs-dialog>
            </div>
        </div>
    </div>
</template>

<script>
import { mapState } from 'vuex';
import spinnerComponent from '@/pages/common/spinner';

export default {
    name: 'Sections',
    components: {
        spinnerComponent,
    },
    data() {
        return {
            editActive: false,
            edit: null,
            editProp: {},
            rename: '',
            newSection: '',
            active3: false,
            loading: true,
        };
    },
    computed: {
        ...mapState({
            sections: (state) => state.sections.sections,
        }),
    },
    created() {
        this.$store.dispatch('sections/initializeSections').then(() => {
            this.loading = false;
        });
    },
    methods: {
        createSection() {
            this.$store.dispatch('sections/create', this.newSection).then(() => {
                this.active3 = false;
                this.newSection = '';
                this.$swal.fire({
                    icon: 'success',
                    title: 'Szakág hozzáadva!',
                    timer: 1500,
                    showConfirmButton: false,
                });
            });
        },
        renameSection(newName) {
            this.$store.dispatch('sections/rename', { newName, section: this.edit }).then(() => {
                this.rename = '';
                this.edit = {};
            });
        },
        deleteSection(section) {
            this.$swal.fire({
                text: 'Biztos törölni szeretné a kiválasztott szakágat?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#FF0000',
                confirmButtonText: 'Igen',
                cancelButtonText: 'Mégsem',
            }).then((choice) => {
                if (choice.isConfirmed === true) {
                    this.$store.dispatch('sections/delete', section).then(() => {
                        this.$swal.fire({
                            icon: 'success',
                            title: 'Szakág törölve!',
                            timer: 1500,
                            showConfirmButton: false,
                        });
                    });
                }
            }).catch(() => {});
        },
    },
};
</script>

<style scoped>
/deep/ .vs-table {
  overflow: unset;
}

/deep/ table {
  min-width: 210px;
}

.con-footer {
  display: flex;
  float: right;
}

/deep/ .vs-input {
  width: 100%;
}

.newSection {
  /*float: right;*/
  max-width: 100%;
  display: flex;
  flex-direction: row-reverse;
}

</style>
