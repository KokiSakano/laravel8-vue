<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        Profile
                    </div>
                    <div class="card-body">
                        <a :href="imgPath[WatchUser.id]" target="_blank"><img class="thumbnail" :src="imgPath[WatchUser.id]" alt="" border="0" /></a>
                    </div>
                    <div>{{WatchUser.name}}</div>
                </div>
                <div class="card">
                    <div class="card-header"> Whisper </div>
                    <p v-if="errored">{{ error }}</p>
                    <p v-if="loading">Loading...</p>
                    <div v-else>
                        <div v-for="whisper in whispers" :key="whisper.id">
                            <div class="card">
                                <div class="card-header">
                                    <img class="thumbnail" :src="imgPath[whisper.user.id]" />
                                    {{ whisper.user.name }}
                                    <a id="time">{{ displayTime(whisper.created_at) }}</a>
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
        </div>
    </div>
</template>

<script>
    import moment from "moment"
    export default {
        props: ['WatchUser', 'AuthUser'],
        data(){
            return {
                loading: true,
                errored: false,
                error: null,
                whispers: null,
                thumbnail: null,
                current_page: 1,
                last_page: 1,
                total: 1,
                from: 0,
                to: 0,
                imgPath: {},
                goodArr: [],
            }
        },
        methods: {
            getWhisper(page){
                axios.get('/api/whispers/profile/'+ this.WatchUser.id +'?page='+page).then((result)=>
                    {
                        this.whispers = result.data["whispers"].data;
                        this.thumbnail = this.WatchUser.thumbnail;
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
                if(this.AuthUser!="null")
                axios.get('/api/good/').then((result)=>
                    {
                        this.goodArr = Object.keys(result.data).map(function (key) {return result.data[key]});
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
            goodOrNot(id){
                if (this.AuthUser==null) return false;
                var flag = false;
                if(this.goodArr.includes(id)){
                    flag=true;
                }
                return flag;
            },
        },
        mounted() {
            this.getWhisper(this.current_page);
            this.getAuthGood();
            console.log(this.AuthUser);
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