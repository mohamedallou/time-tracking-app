<template>
    <v-container>
        <v-card max-width="900" max-height="900" variant="outlined">
            <v-card-title>
                Time Tracking
            </v-card-title>
            <v-card-subtitle>
                Click on the button to start time tracking
            </v-card-subtitle>

            <v-card-text>
                <StopWatch ref="watch"></StopWatch>
               <v-row justify="space-between">
                   <v-col cols="2" v-if="useTrackingStore().currentLog === null">
                       <v-btn icon="mdi-timer-play" color="primary" size="large" @click="startTracking">
                       </v-btn>
                   </v-col>
                   <v-col cols="2" v-if="useTrackingStore().currentLog !== null && !stopped">
                       <v-btn icon="mdi-timer-stop" color="red" size="large" @click="stopTracking">
                       </v-btn>
                   </v-col>
                   <v-col cols="2" v-if="useTrackingStore().currentLog !== null && stopped">
                       <v-btn icon="mdi-timer-refresh" color="primary" size="large" @click="resetTracking">
                       </v-btn>
                   </v-col>
               </v-row>
            </v-card-text>
        </v-card>
    </v-container>
</template>
<script>
import {useTrackingStore} from "@/stores/tracking.store.js";
import {defineComponent} from "vue";
import StopWatch from "@/components/StopWatch.vue";

export default defineComponent({
    components: {StopWatch},
    data() {
        return {
            stopped: false,
        }
    },
    methods: {
        useTrackingStore,
        startTracking() {
            this.useTrackingStore().startTracking();
            this.$refs.watch.startClock();
        },
        stopTracking() {
            this.useTrackingStore().stopTracking();
            this.$refs.watch.stopClock();
            this.stopped = true;
        },
        resetTracking() {
            this.$refs.watch.resetClock();
            this.stopped = false;
            this.useTrackingStore().resetTracking();
        }
    }
})

</script>
