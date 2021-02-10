<template>
    <div class="card">
        <p v-if="errored">{{ error }}</p>
        <p v-if="loading">Loading...</p>
        <div v-else>
            <ul>
                <div v-for="whisper in whispers" :key="whisper.id">
                    <div class="card">
                        <div class="card-header">
                            {{ whisper.name }}
                            <a id="time">{{ displayTime(whisper.created_at) }}</a>
                        </div>
                        <div class="card-body">
                        {{ whisper.whisper }}
                        </div>
                    </div>
                    <br />
                </div>
            </ul>
        </div>
    </div>
</template>

<script>
    export default {
        data(){
            return {
                loading: true,
                errored: false,
                error: null,
                whispers:null,
            };
        },
        methods:{
            getWhisper(){
                axios.get('/api/').then((result)=>
                    {
                        this.whispers = result.data
                        console.log(this.whispers)
                    })
                    .catch(err => {
                        (this.errored = true), (this.error = err);
                    })
                    .finally(() => (this.loading = false)
                );
            },
            displayTime(time){
                const time_list = time.split("-");
                let time_info = {"year": time_list[0], "mounth": time_list[1], "day": time_list[2].substr(0, 2), "time": time_list[2].substr(3, 8)};
                // 後から現在の時刻と比較して表示を変更するコードに書き換える。
                return time_info["year"] + "/" + time_info["mounth"] + "/" + time_info["day"] + "/" + time_info["time"];
            },
        },
        created() {
            this.getWhisper()
        }
    }
</script>
<style>
    #time{
        opacity: 0.6;
    }
</style>