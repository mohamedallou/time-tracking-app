import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import AdminArea from "@/views/admin/AdminArea.vue";
import TimeLogOverview from "@/views/admin/TimeLogOverview.vue";
import TimeLogReportView from "@/views/admin/TimeLogReportView.vue";
import {authGuard} from "@/security/guard.js";

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView,
      beforeEnter: authGuard,
    },
    {
      path: '/error',
      name: 'error',
      component: Error
    },
    {
      path: '/admin',
      name: 'Admin',
      component: AdminArea,
      children: [
        {
          path: '',
          name: 'TimeLogsOverview',
          component: TimeLogOverview,
          alias: 'timelogs-list'
        },
        {
          path: 'reports',
          name: 'Reports',
          component: TimeLogReportView,
          alias: 'reports'
        },
      ],
    },
  ]
})

export default router
