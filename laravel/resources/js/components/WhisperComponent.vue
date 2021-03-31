<template>
    <div>
        <div class="card">
            <textarea type="text" class="form-control" v-model="whisperForm" placeholder="say whisper" rows=3></textarea>
            <button type="submit" class="btn btn-primary" id="btn-whisper" @click="postWhisper">Whisper</button>
        </div>
        <div class="card-header"> Whisper </div>
        <p v-if="errored">{{ error }}</p>
        <p v-if="loading">Loading...</p>
        <div v-else>
            <div v-for="whisper in whispers" :key="whisper.id">
                <div class="card mx-auto">
                    <div class="card-header">
                        <img @click="showProfile(whisper.user.id)" class="thumbnail" :src="imgPath[whisper.user.id]"/>
                        <a @click="showProfile(whisper.user.id)">{{ whisper.user.name }}</a>
                        <a id="time">{{ displayTime(whisper.created_at) }}</a>
                        <div v-if="whisper.user_id === authId">
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
            <EditwhispModal @close="closeEditModal" v-if="editModal">
                <p slot="header"> Whisp変更 </p>
                <div slot="body">
                    <p>Whisp:<input type="text" class="form-control" v-model="whispForm" :placeholder="whispEdit.whisp"></p>
                </div>
                <button slot="footer" @click="changeWhisp()">
                OK
                </button>
            </EditwhispModal>
        </div>

        <div class="row">
            <div class="col-sm-6 mx-auto">
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
</template>

<script>
    import moment from 'moment';
    import EditwhispModal from './EditwhispModal';
    import ReplyModal from './ReplyModal';
    export default {
        components: {EditwhispModal, ReplyModal},
        data(){
            return {
                loading: true,
                errored: false,
                error: null,
                whispers: null,
                editModal: false,
                replyModal: false,
                whisperForm: null,
                whispEdit: null,
                whispForm: null,
                whispReply: {id: null},
                replyForm: null,
                authId: null,
                current_page: 1,
                last_page: 1,
                total: 1,
                from: 0,
                to: 0,
                imgPath: [],
                goodWhispArr: [],
                goodReplyArr: [],
                replies: [],
            };
        },
        methods:{
            getWhisper(page){
                axios.get('/api/whispers?page=' + page).then((result)=>
                    {
                        this.whispers = result.data["whispers"].data;
                        this.authId = result.data["loginUserId"];
                        this.current_page = result.data["whispers"].current_page;
                        this.last_page = result.data["whispers"].last_page;
                        this.total = result.data["whispers"].total;
                        this.from = result.data["whispers"].from;
                        this.to = result.data["whispers"].to;
                        this.imgPath = {...this.imgPath, ...result.data["imgPath"]};
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
            postWhisper(){
                var data = {
                    whisper: this.whisperForm
                };
                if (!data["whisper"]){
                    alert("何か入力してください。");
                }
                else if(data["whisper"].length>120){
                    alert("文字列が長すぎます。120文字以下にして下さい。");
                }
                else {
                    axios.post('/api/whispers/', data).then(() =>
                        {
                            this.getWhisper(this.current_page);
                            this.whisperForm = "";
                        })
                        .catch(err => {
                            (this.errored = true), (this.error = err);
                        })
                        .finally(() => (this.loading = false)
                    );
                }
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
                    if(!!this.whispReply) this.whispReply.whisp= this.whispForm;
                    axios.put("/api/whispers/" + id, {
                        whisp: this.whispForm,
                    }).then(() =>
                        {
                            this.getWhisper(this.current_page);
                            this.whispEdit = null;
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
            openEditModal(whisper) {
                this.editModal = true;
                this.whispEdit = whisper;
            },
            closeEditModal() {
                this.editModal = false;
                this.whispForm = null;
            },
            changeWhisp() {
                if (this.whispEdit.whisp !== this.whispForm){
                    if(!this.whispEdit.whisper_id)this.editWhisper(this.whispEdit.id);
                    else this.editReply(this.whispEdit.id);
                }
                this.closeEditModal();
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
            change(page) {
                if (page >= 1 && page <= this.last_page){
                    this.getWhisper(page);
                    this.getAuthGood();
                }
            },
            showProfile(userId){
                location.href = "http://localhost/profile/"+userId;
            },
            goodOrNot(id, type){
                var flag = false;
                if((this.goodWhispArr.includes(id) && type!="reply") || (this.goodReplyArr.includes(id) && type=="reply")){
                    flag=true;
                }
                return flag;
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
    #time{
        opacity: 0.6;
    }
    #btn-whisper{
        float: right;
    }
    #somefunc{
        float: right;
    }
    .thumbnail {
    border-radius: 50%;
    width:  50px;
    height: 50px;
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
    .comment-btn {
    width: 25px;
    height: 30px;
    font-size: 25px;
    color: #808080; 
    margin-left: 20px;
    }
</style>