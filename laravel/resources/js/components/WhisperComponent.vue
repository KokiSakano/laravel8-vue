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
                                    <button class="dropdown-item" type="button" @click="openModal(whisper)">編集</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        {{ whisper.whisp }}
                        <div class="text-right">
                            <a @click="doGood(whisper.id)">
                                <i class="fas fa-star" :class="[goodOrNot(whisper.id)? 'unlike-btn' : 'like-btn']" />
                            </a>
                            <a>
                                {{ whisper.good }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <EditwhispModal @close="closeModal" v-if="modal">
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
    export default {
        components: {EditwhispModal},
        data(){
            return {
                loading: true,
                errored: false,
                error: null,
                whispers: null,
                modal: false,
                whisperForm: null,
                whispEdit: null,
                whispForm: null,
                authId: null,
                current_page: 1,
                last_page: 1,
                total: 1,
                from: 0,
                to: 0,
                imgPath: [],
                goodArr: [],
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
                        this.imgPath = result.data["imgPath"];
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
                        this.goodArr = Object.keys(result.data).map(function (key) {return result.data[key]});
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
            doGood(id){
                if (this.goodOrNot(id)){
                    axios.post("/api/good/m/"+id).then(() =>
                        {
                            this.getWhisper(this.current_page);
                            this.getAuthGood();
                        })
                        .catch(err => {
                            (this.errored = true), (this.error = err);
                        })
                        .finally(() => (this.loading = false)
                    );
                }
                else{
                    axios.post("/api/good/p/"+id).then(() =>
                        {
                            this.getWhisper(this.current_page);
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
            openModal(whisper) {
                this.modal = true;
                this.whispEdit = whisper;
                console.log(this.whispEdit)
            },
            closeModal() {
                this.modal = false;
            },
            changeWhisp() {
                if (this.whispEdit.whisp !== this.whispForm){
                    this.editWhisper(this.whispEdit.id);
                }
                this.closeModal();
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
            goodOrNot(id){
                var flag = false;
                if(this.goodArr.includes(id)){
                    flag=true;
                }
                console.log(flag)
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
</style>