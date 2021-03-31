<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        Profile
                    </div>
                    <div class="card-body">
                        <a :href="imgPath[authId]" target="_blank"><img class="thumbnail" :src="imgPath[authId]" alt="" border="0" /></a>
                        <button @click="openProfileModal">
                            プロフィール変更
                        </button>
                        <EditprofileModal @close="closeProfileModal" v-if="profileModal">
                            <p slot="header"> プロフィール変更 </p>
                            <div slot="body">
                                <div class="card-body">
                                    <h4 class="card-title">プロフィール画像変更</h4>
                                    <input type="file" accept="image/*" @change="changeImage($event)">
                                    <img class="thumbnail-change" :src="cThumbnail">
                                </div>
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
                <div class="card">
                    <div class="card-header"> Whisper </div>
                    <p v-if="errored">{{ error }}</p>
                    <p v-if="loading">Loading...</p>
                    <div v-else>
                        <div v-for="whisper in whispers" :key="whisper.id">
                            <div v-if="whisper.user_id === authId" class="card">
                                <div class="card-header">
                                    <img class="thumbnail" :src="imgPath[authId]"/>
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
                                            <button class="dropdown-item" type="button" @click="openEditModal(whisper)">編集</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    {{ whisper.whisp }}
                                    <div class="text-right">
                                        <a @click="openReplyModal(whisper)">
                                                <i class="fas fa-comment comment-btn"></i>
                                        </a>
                                        <a @click="doGood(whisper.id, 'whisp1')">
                                            <i class="fas fa-star" :class="[goodOrNot(whisper.id, 'whisp1')? 'unlike-btn' : 'like-btn']" />
                                        </a>
                                        <a>
                                            {{ whisper.good }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <ul class="pagination justify-content-center">
                            <li class="page-item" :class="{disabled: current_page <= 1}"><a class="page-link" href="#" @click="change(1)">&laquo;</a></li>
                            <li class="page-item" :class="{disabled: current_page <= 1}"><a class="page-link" href="#" @click="change(current_page - 1)">&lt;</a></li>
                            <li v-for="page in pages" :key="page" class="page-item" :class="{active: page === current_page}">
                                <a class="page-link" href="#" @click="change(page)">{{page}}</a>
                            </li>
                            <li class="page-item" :class="{disabled: current_page >= last_page}"><a class="page-link" href="#" @click="change(current_page + 1)">&gt;</a></li>
                            <li class="page-item" :class="{disabled: current_page >= last_page}"><a class="page-link" href="#" @click="change(last_page)">&raquo;</a></li>
                        </ul>
                    </div>
                    <div style="margin-top: 40px" class="col-sm-6 text-right">全 {{total}} 件中 {{from}} 〜 {{to}} 件表示</div>
                </div>
            </div>
            <ReplyModal @close="closeReplyModal" v-if="replyModal">
                <div slot="header" class="card mx-auto">
                    <div class="card-header">
                        Reply Message
                    </div>
                </div>
                <div slot="body">
                    <div class="card">
                        <div class="card-header">
                            <img @click="showProfile(whispReply.user.id)" class="thumbnail" :src="imgPath[whispReply.user.id]"/>
                            <a @click="showProfile(whispReply.user.id)">{{ whispReply.user.name }}</a>
                            <a id="time">{{ displayTime(whispReply.created_at) }}</a>
                            <div v-if="whispReply.user_id === authId">
                                <div class="dropdown" id="somefunc">
                                    <button type="button" id="dropdown1"
                                        class="btn btn-secondary dropdown-toggle"
                                        data-toggle="dropdown"
                                        aria-haspopup="true"
                                        aria-expanded="false">
                                        ⋮
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdown1">
                                        <button class="dropdown-item" type="button" @click="deleteWhisper(whispReply.id)">削除</button>
                                        <button class="dropdown-item" type="button" @click="openEditModal(whispReply)">編集</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            {{ whispReply.whisp }}
                            <div class="text-right">
                                <a @click="doGood(whispReply.id, 'whisp2')">
                                    <i class="fas fa-star" :class="[goodOrNot(whispReply.id, 'whisp2')? 'unlike-btn' : 'like-btn']" />
                                </a>
                                <a>
                                    {{ whispReply.good }}
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            Reply
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item" v-for="reply in replies" :key="reply.id">
                                <div>
                                    <img @click="showProfile(reply.user.id)" class="thumbnail" :src="imgPath[reply.user.id]"/>
                                    <a @click="showProfile(reply.user.id)">{{ reply.user.name }}</a>
                                    <a id="time">{{ displayTime(reply.created_at) }}</a>
                                    <div v-if="reply.user_id === authId">
                                        <div class="dropdown" id="somefunc">
                                            <button type="button" id="dropdown1"
                                                class="btn btn-secondary dropdown-toggle"
                                                data-toggle="dropdown"
                                                aria-haspopup="true"
                                                aria-expanded="false">
                                                ⋮
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="dropdown1">
                                                <button class="dropdown-item" type="button" @click="deleteReply(reply.id)">削除</button>
                                                <button class="dropdown-item" type="button" @click="openEditModal(reply)">編集</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    {{ reply.whisp }}
                                    <div class="text-right">
                                        <a @click="doGood(reply.id, 'reply')">
                                            <i class="fas fa-star" :class="[goodOrNot(reply.id, 'reply')? 'unlike-btn' : 'like-btn']" />
                                        </a>
                                        <a>
                                            {{ reply.good }}
                                        </a>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <p>Reply:<input type="text" class="form-control" v-model="replyForm" placeholder="120文字以下で入力してください。"></p>
                    <div class="text-right">
                        <button @click="PostReply(whispReply.id)">
                        OK
                        </button>
                    </div>
                </div>
            </ReplyModal>
            <EditwhispModal @close="closeEditModal" v-if="EditModal">
                <p slot="header"> Whisp変更 </p>
                <div slot="body">
                    <p>変更前:{{whispEdit.whisp}}</p> <br />
                    <p>変更後:<input type="text" class="form-control" v-model="whispForm"></p>
                </div>
                <button slot="footer" @click="changeWhisp()">
                OK
                </button>
            </EditwhispModal>
        </div>
    </div>
</template>

<script>
    import moment from "moment"
    import EditprofileModal from "./EditprofileModal";
    import EditwhispModal from "./EditwhispModal";
    import ReplyModal from "./ReplyModal";
    export default {
        components: {
            EditprofileModal,
            EditwhispModal,
            ReplyModal,
        },
        data(){
            return {
                loading: true,
                errored: false,
                error: null,
                profileModal: false,
                EditModal: false,
                replyModal: false,
                whispers: null,
                whispReply: {id: null},
                authId: null,
                loginUser: null,
                thumbnailFlag: false,
                file: null,
                whispEdit: null,
                nameForm: null,
                emailForm: null,
                whispForm: null,
                replyForm: null,
                passwordForm: "まだ対応してないよ。",
                deleteForm: null,
                current_page: 1,
                last_page: 1,
                total: 1,
                from: 0,
                to: 0,
                imgPath: {},
                cThumbnail: null,
                goodWhispArr: [],
                goodReplyArr: [],
                replies: [],
            }
        },
        methods: {
            getWhisper(page){
                axios.get('/api/whispers/myprofile?page='+page).then((result)=>
                    {
                        this.loginUser = result.data["loginUser"];
                        this.authId = this.loginUser["id"];
                        this.nameForm = this.loginUser["name"];
                        this.emailForm = this.loginUser["email"];
                        this.imgPath = {...this.imgPath, ...result.data["imgPath"]};
                        this.cThumbnail = this.imgPath[this.authId];
                        this.whispers = result.data["whispers"].data;
                        this.current_page = result.data["whispers"].current_page;
                        this.last_page = result.data["whispers"].last_page;
                        this.total = result.data["whispers"].total;
                        this.from = result.data["whispers"].from;
                        this.to = result.data["whispers"].to;
                        this.whispReply = this.whispers.find(whisper => whisper.id === this.whispReply.id);
                    })
                    .catch(err => {
                        (this.errored = true), (this.error = err);
                    })
                    .finally(() => (this.loading = false)
                );
            },
            getAuthGood(){
                axios.get('/api/good/').then((result)=>
                    {
                        this.goodWhispArr = Object.keys(result.data["authWhisperGoods"]).map(function (key) {return result.data["authWhisperGoods"][key]});
                        this.goodReplyArr = Object.keys(result.data["authReplyGoods"]).map(function (key) {return result.data["authReplyGoods"][key]});
                    }
                );
            },
            getReply(id){
                axios.get('/api/replies/'+id).then
                (
                    (result)=>{
                        this.replies = result.data["replies"];
                        this.imgPath = {...this.imgPath, ...result.data["imgPath"]};
                    }
                );
            },
            deleteWhisper(id){
                var data = {};
                axios.delete("/api/whispers/" + id, JSON.stringify(data)).then(() =>
                    {
                        this.getWhisper(this.current_page);
                    })
                    .catch(err => {
                        (this.errored = true), (this.error = err);
                    })
                    .finally(() => (this.loading = false)
                );
            },
            deleteReply(id){
                var data = {};
                axios.delete("/api/replies/" + id, JSON.stringify(data)).then(() =>
                    {
                        this.getReply(this.whispReply.id);
                    })
                    .catch(err => {
                        (this.errored = true), (this.error = err);
                    })
                    .finally(() => (this.loading = false)
                );
            },
            editWhisper(id){
                if(this.whispForm.length>120){
                    alert("文字列が長すぎます。120文字以下にして下さい。");
                }
                else{
                    axios.put("/api/whispers/" + id, {
                        whisp: this.whispForm,
                    }).then(() =>
                        {
                            this.getWhisper(this.current_page);
                            this.whispEdit = null;
                            this.whispForm = null;
                        })
                        .catch(err => {
                            (this.errored = true), (this.error = err);
                        })
                        .finally(() => (this.loading = false)
                    );
                }
            },
            editReply(id){
                if(this.whispForm.length>120){
                    alert("文字列が長すぎます。120文字以下にして下さい。");
                }
                else{
                    axios.put("/api/replies/" + id, {
                        whisp: this.whispForm,
                    }).then(() =>
                        {
                            this.getReply(this.whispReply.id);
                            this.whispEdit = null;
                        })
                        .catch(err => {
                            (this.errored = true), (this.error = err);
                        })
                        .finally(() => (this.loading = false)
                    );
                }
            },
            displayTime(time){
                const timeMoment = moment(time);
                const nowMoment = moment(new Date());
                const timeUnits = ["years", "months", "weeks", "days", "hours", "minutes", "seconds"];
                const unit = timeUnits.filter(timeUnit => {
                    return nowMoment.diff(timeMoment, timeUnit) != 0;
                })[0];
                if (unit === "year" || unit ==="months") return timeMoment.format("YYYY/MM/DD");
                else if(!!unit) return nowMoment.diff(timeMoment, unit)+unit;
                else return "now"
            },
            openProfileModal() {
                this.profileModal = true;
            },
            closeProfileModal() {
                this.profileModal = false;
                this.thumbnailFlag = false;
            },
            openEditModal(whisper) {
                this.EditModal = true;
                this.whispEdit = whisper;
            },
            closeEditModal() {
                this.EditModal = false;
            },
            openReplyModal(whisper) {
                this.replyModal = true;
                this.whispReply = whisper;
                this.getReply(whisper.id);
            },
            closeReplyModal() {
                this.replyModal = false;
                this.whispReply = {id: null};
            },
            changeImage(e) {
                const files = e.target.files;
                if(files.length > 0) {
                    this.file = files[0];
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.thumbnailFlag = true
                        this.cThumbnail = e.target.result;
                    };
                    reader.readAsDataURL(this.file);
                }
            },
            changeProfile() {
                if(this.deleteForm === "delete"){
                    this.deleteUser();
                }
                else if (this.loginUser["name"] !== this.nameForm || this.thumbnailFlag){
                    if (this.nameForm.length > 10){
                        alert("名前が長すぎます。")
                    }
                    else{
                        this.editUser();
                    }
                }
                this.closeProfileModal();
            },
            changeWhisp() {
                if (this.whispEdit.whisp !== this.whispForm){
                    if(!this.whispEdit.whisper_id)this.editWhisper(this.whispEdit.id);
                    else this.editReply(this.whispEdit.id);
                }
                this.closeEditModal();
            },
            editUser(){
                let formData = new FormData();
                formData.append("name", this.nameForm);
                formData.append("email", this.emailForm);
                formData.append("password", this.passwordForm);
                formData.append("file", this.file);
                axios.post('/api/users/' + this.loginUser["id"], formData, {
                    headers: {
                        'X-HTTP-Method-Override': 'PUT'
                    }
                })
                .then(() => this.getWhisper(this.current_page))
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
            },
            doGood(id, type){
                var data = {
                    "id": id,
                    "whispType": type,
                };
                if (this.goodOrNot(id, type)){
                    axios.post("/api/good/m/", data).then(() =>
                        {
                            this.getWhisper(this.current_page);
                            if (type=="reply") this.getReply(this.whispReply.id);
                            this.getAuthGood();
                        })
                        .catch(err => {
                            (this.errored = true), (this.error = err);
                        })
                        .finally(() => (this.loading = false)
                    );
                }
                else{
                    axios.post("/api/good/p/", data).then(() =>
                        {
                            this.getWhisper(this.current_page);
                            if (type=="reply") this.getReply(this.whispReply.id);
                            this.getAuthGood();
                        })
                        .catch(err => {
                            (this.errored = true), (this.error = err);
                        })
                        .finally(() => (this.loading = false)
                    );
                }
            },
            goodOrNot(id, type){
                var flag = false;
                if((this.goodWhispArr.includes(id) && type!="reply") || (this.goodReplyArr.includes(id) && type=="reply")){
                    flag=true;
                }
                return flag;
            },
            PostReply(id) {
                var data = {
                    reply: this.replyForm,
                    whispType: "whisp",
                    id: id,
                };
                if (!data["reply"]){
                    alert("何か入力してください。");
                }
                else if(data["reply"].length>120){
                    alert("文字列が長すぎます。120文字以下にして下さい。");
                }
                else {
                    axios.post('/api/replies/', data).then(() =>
                        {
                            this.getReply(id);
                            this.replyForm = "";
                        })
                        .catch(err => {
                            (this.errored = true), (this.error = err);
                        })
                        .finally(() => (this.loading = false)
                    );
                }
            },
        },
        mounted() {
            this.getWhisper(this.current_page);
            this.getAuthGood();
        },
        computed: {
        pages() {
            let start = _.max([this.current_page - 2, 1])
            let end = _.min([start + 5, this.last_page + 1])
            start = _.max([end - 5, 1])
            return _.range(start, end)
        },
        }
    }
</script>
<style>
    .thumbnail {
        border-radius: 50%;
        width:  50px;
        height: 50px;
    }
    .thumbnail-change {
        border-radius: 50%;
        width: 100px;
        height: 100px;
    }
    .like-btn {
    width: 25px;
    height: 30px;
    font-size: 25px;
    color: #808080; 
    margin-left: 20px;
    }
    .unlike-btn {
    width: 25px;
    height: 30px;
    font-size: 25px;
    color: #e54747;
    margin-left: 20px;
    }
</style>