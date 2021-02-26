<template>
    <div>
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
            };
        },
        methods:{
            getWhisper(){
                axios.get('/api/whispers/noauth/').then((result)=>
                    {
                        this.whispers = result.data.reverse();
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
</style>