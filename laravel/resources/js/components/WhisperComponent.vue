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
            <ul>
                <br />
                <div v-for="whisper in whispers" :key="whisper.id">
                    <div class="card">
                        <div class="card-header">
                            {{ whisper.user.name }}
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
                                    </div>
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
                whisperForm: null,
                authId: null,
            };
        },
        methods:{
            getWhisper(){
                axios.get('/api/whispers/').then((result)=>
                    {
                        this.whispers = result.data["whispers"].reverse();
                        this.authId = result.data["loginUserId"];
                    })
                    .catch(err => {
                        (this.errored = true), (this.error = err);
                    })
                    .finally(() => (this.loading = false)
                );
            },
            postWhisper(){
                var data = {
                    whisper: this.whisperForm
                };
                if (!!data["whisper"]) {
                    axios.post('/api/whispers/', data).then(() =>
                        {
                            this.getWhisper();
                        })
                        .catch(err => {
                            (this.errored = true), (this.error = err);
                        })
                        .finally(() => (this.loading = false)
                    );
                }
                this.whisperForm = "";
            },
            deleteWhisper(id){
                var data = {};
                axios.delete("/api/whispers/" + id, JSON.stringify(data)).then(() =>
                    {
                        this.getWhisper();
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
        },
        created() {
            this.getWhisper();
        },
        mounted() {
            this.getWhisper();
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
</style>