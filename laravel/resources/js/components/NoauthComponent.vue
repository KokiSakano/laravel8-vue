<template>
    <div>
        <div class="card-header"> Whisper </div>
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
        <p v-if="errored">{{ error }}</p>
        <p v-if="loading">Loading...</p>
        <div v-else>
            <ul>
                <br />
                <div v-for="whisper in whispers" :key="whisper.id">
                    <div class="card">
                        <div class="card-header">
                            <!--img class="thumbnail" :src="whisper.user.thumbnail" width="10" height="10"/-->
                            <img class="thumbnail" src="default.png"/>
                            {{ whisper.user.name }}
                            <a id="time">{{ displayTime(whisper.created_at) }}</a>
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
</template>

<script>
    import moment from 'moment';
    export default {
        data(){
            return {
                loading: true,
                errored: false,
                error: null,
                whispers: null,
                current_page: 1,
                last_page: 1,
                total: 1,
                from: 0,
                to: 0
            };
        },
        methods:{
            getWhisper(page){
                axios.get('/api/whispers/noauth?page='+page).then((result)=>
                    {
                        this.whispers = result.data.data;
                        this.current_page = result.data.current_page;
                        this.last_page = result.data.last_page;
                        this.total = result.data.total;
                        this.from = result.data.from;
                        this.to = result.data.to;
                    })
                    .catch(err => {
                        (this.errored = true), (this.error = err);
                    })
                    .finally(() => (this.loading = false)
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
            change(page) {
            if (page >= 1 && page <= this.last_page) this.getWhisper(page)
            },
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
</style>