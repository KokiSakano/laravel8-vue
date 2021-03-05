/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
import './bootstrap';
import ExampleComponent from "./components/ExampleComponent";
import WhisperComponent from "./components/WhisperComponent.vue";
import NoauthComponent from "./components/NoauthComponent";
import MyprofileComponent from "./components/MyprofileComponent.vue";

window.Vue = require('vue');

const app = new Vue({
    el: "#app",
    components: {
        ExampleComponent,
        WhisperComponent,
        NoauthComponent,
        MyprofileComponent,
    }
});
