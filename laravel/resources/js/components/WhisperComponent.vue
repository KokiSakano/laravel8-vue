<template>
    <div>
        <div class="card">
            <textarea type="text" class="form-control" v-model="whisper_form" placeholder="say whisper" rows=3></textarea>
            <button type="submit" class="btn btn-primary" id="btn-whisper" @click="postWhisper">Whisper</button>
        </div>
        <div class="card-header"> Whisper </div>
        <p v-if="errored">{{ error }}</p>
        <p v-if="loading">Loading...</p>
        <div v-else>
            <ul>
                <div v-for="whisper in whispers" :key="whisper.id">
                    <div class="card">
                        <div class="card-header">
                            {{ whisper.name }}
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
                whisper_form: null,
            };
        },
        methods:{
            getWhisper(){
                axios.get('/api/').then((result)=>
                    {
                        this.whispers = result.data.reverse()
                    })
                    .catch(err => {
                        (this.errored = true), (this.error = err);
                    })
                    .finally(() => (this.loading = false)
                );
            },
            postWhisper(){
                var data = {
                    whisper: this.whisper_form
                };
                if (!!this.whisper) {
                    axios.post('/api/', data).then(() =>
                        {
                            this.getWhisper();
                        })
                        .catch(err => {
                            (this.errored = true), (this.error = err);
                        })
                        .finally(() => (this.loading = false)
                    );
                }
                this.whisper_form = "";
            },
            deleteWhisper(id){
                var data = {};
                axios.delete("/api/" + id, JSON.stringify(data)).then(() =>
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
                const time_list = time.split("-");
                let time_info = {"year": time_list[0], "mounth": time_list[1], "day": time_list[2].substr(0, 2), "time": time_list[2].substr(3, 8)};
                // 後から現在の時刻と比較して表示を変更するコードに書き換える。
                return time_info["year"] + "/" + time_info["mounth"] + "/" + time_info["day"] + "/" + time_info["time"];
            },
        },
        mounted() {
            this.getWhisper()
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