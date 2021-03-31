<template>
    <div>
        <div class="card-header"> Whisper </div>
        <p v-if="errored">{{ error }}</p>
        <p v-if="loading">Loading...</p>
        <div v-else>
            <div v-for="whisper in whispers" :key="whisper.id">
                <div class="card">
                    <div class="card-header">
                        <img @click="showProfile(whisper.user.id)" class="thumbnail" :src="imgPath[whisper.user.id]"/>
                        <a @click="showProfile(whisper.user.id)">{{ whisper.user.name }}</a>
                        <a id="time">{{ displayTime(whisper.created_at) }}</a>
                    </div>
                    <div class="card-body">
                        {{ whisper.whisp }}
                        <div class="text-right">
                            <a @click="openReplyModal(whisper)">
                                <i class="fas fa-comment comment-btn"></i>
                            </a>
                            <a>
                                <i class="fas fa-star like-btn" />
                            </a>
                            <a>
                                {{ whisper.good }}
                            </a>
                        </div>
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
                    </div>
                    <div class="card-body">
                        {{ whispReply.whisp }}
                        <div class="text-right">
                            <a>
                                <i class="fas fa-star like-btn" />
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
                            </div>
                            <div>
                                {{ reply.whisp }}
                                <div class="text-right">
                                    <a>
                                        <i class="fas fa-star like-btn" />
                                    </a>
                                    <a>
                                        {{ reply.good }}
                                    </a>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </ReplyModal>
        <div class="row">
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
</template>

<script>
    import moment from 'moment';
    import ReplyModal from './ReplyModal';
    export default {
        components: {ReplyModal},
        data(){
            return {
                loading: true,
                errored: false,
                error: null,
                whispers: null,
                whispReply: null,
                replyModal: null,
                current_page: 1,
                last_page: 1,
                total: 1,
                from: 0,
                to: 0,
                imgPath: [],
                replies: [],
            };
        },
        methods:{
            getWhisper(page){
                axios.get('/api/whispers/noauth?page='+page).then((result)=>
                    {
                        this.whispers = result.data["whispers"].data;
                        this.current_page = result.data["whispers"].current_page;
                        this.last_page = result.data["whispers"].last_page;
                        this.total = result.data["whispers"].total;
                        this.from = result.data["whispers"].from;
                        this.to = result.data["whispers"].to;
                        this.imgPath = {...this.imgPath, ...result.data["imgPath"]};
                    })
                    .catch(err => {
                        (this.errored = true), (this.error = err);
                    })
                    .finally(() => (this.loading = false)
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
            openReplyModal(whisper) {
                this.replyModal = true;
                this.whispReply = whisper;
                this.getReply(whisper.id);
            },
            closeReplyModal() {
                this.replyModal = false;
                this.whispReply = null;
            },
            change(page) {
            if (page >= 1 && page <= this.last_page) this.getWhisper(page)
            },
            showProfile(userId){
                location.href = "http://localhost/profile/"+userId;
            }
        },
        created() {
            this.getWhisper(this.current_page);
        },
        mounted() {
            this.getWhisper(this.current_page);
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
</style>