<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        Profile
                    </div>
                    <div class="card-body">
                        <button @click="openModal">
                            プロフィール変更
                        </button>
                        <EditprofileModal @close="closeModal" v-if="modal">
                            <p slot="header"> プロフィール変更 </p>
                            <div slot="body">
                                <tr>
                                    <td align="right"><b>name:</b></td>
                                    <td><input type="text" class="form-control" v-model="nameForm"></td>
                                </tr>
                                <tr>
                                    <td align="right"><b>email:</b></td>
                                    <td><input type="text" class="form-control" v-model="emailForm"></td>
                                </tr>
                                <tr>
                                    <td align="right"><b>password:</b></td>
                                    <td><input type="text" class="form-control" v-model="passwordForm"></td>
                                </tr>
                                <tr>
                                    <td align="right"><b>アカウント削除:</b></td>
                                    <td><input type="text" class="form-control" v-model="deleteForm" placeholder="deleteと入力してください。"></td>
                                </tr>
                            </div>
                            <button slot="footer" @click="changeProfile()">
                            OK
                            </button>
                        </EditprofileModal>
                    </div>
                </div>
                <p v-if="errored">{{ error }}</p>
                <p v-if="loading">Loading...</p>
                <div v-else>
                    <ul>
                        <div v-for="whisper in whispers" :key="whisper.id">
                            <div v-if="whisper.user_id === authId" class="card">
                                <div class="card-header">
                                    {{ whisper.user.name }}
                                    <a id="time">{{ displayTime(whisper.created_at) }}</a>
                                    <div class="dropdown" id="somefunc">
                                        <button type="button" id="dropdown1"
                                            class="btn btn-secondary dropdown-toggle"
                                            data-toggle="dropdown"
                                            aria-haspopup="true"
                                            aria-expanded="false">
                                            ⋮
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdown1">
                                            <button class="dropdown-item" type="button" @click="deleteWhisper(whisper.id)">削除</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    {{ whisper.whisp }}
                                </div>
                            </div>
                            <br />
                        </div>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import moment from "moment"
    import EditprofileModal from "./EditprofileModal";
    export default {
        components: {EditprofileModal},
        data(){
            return {
                loading: true,
                errored: false,
                error: null,
                modal: false,
                whispers: null,
                authId: null,
                loginUser: null,
                nameForm: null,
                emailForm: null,
                passwordForm: "まだ対応してないよ。",
                deleteForm: null,
            }
        },
        methods: {
            getWhisper(){
                axios.get('/api/whispers/').then((result)=>
                    {
                        this.whispers = result.data["whispers"].reverse();
                        this.authId = result.data["loginUserId"];
                        this.getLoginUser();
                    })
                    .catch(err => {
                        (this.errored = true), (this.error = err);
                    })
                    .finally(() => (this.loading = false)
                );
            },
            getLoginUser(){
                axios.get('/api/users/'+this.authId).then((result)=>
                    {
                        this.loginUser = result.data;
                        this.nameForm = this.loginUser["name"];
                        this.emailForm = this.loginUser["email"];
                    }
                );
            },
            displayTime(time){
                const timeMoment = moment(time);
                const nowMoment = moment(new Date());
                const timeUnits = ["years", "months", "weeks", "days", "hours", "minutes", "seconds"];
                const unit = timeUnits.filter(timeUnit => {
                    return nowMoment.diff(timeMoment, timeUnit) != 0;
                })[0];
                if (unit === "year" || unit ==="months") return timeMoment.format("YYY/MM/DD");
                else if(!!unit) return nowMoment.diff(timeMoment, unit)+unit;
                else return "now"
            },
            openModal() {
                this.modal = true
            },
            closeModal() {
                this.modal = false
            },
            changeProfile() {
                if(this.deleteForm === "delete"){
                    this.deleteUser();
                }
                else if (this.loginUser["name"] !== this.nameForm){
                    this.editUser();
                }
                this.closeModal();
            },
            editUser(){
                axios.put('/api/users/' + this.loginUser["id"], {
                    name: this.nameForm,
                    email: this.emailForm,
                    password: this.passwordForm
                })
                .then(() => this.getWhisper())
                .catch(err => {
                    (this.errored = true), (this.error = err);
                    }
                );
            },
            deleteUser(){
                axios.delete("/api/users/" + this.loginUser["id"]).then(() =>
                    {
                        location.href = "/register";
                    })
                    .catch(err => {
                        (this.errored = true), (this.error = err);
                    })
                    .finally(() => (this.loading = false)
                );
            }
        },
        mounted() {
            this.getWhisper();
        }
    }
</script>
