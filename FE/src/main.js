import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import { cauHinhAxiosToanCuc, kichHoatFetchBangAxios } from './services/httpClient'

// Import global CSS
import '@fortawesome/fontawesome-free/css/all.min.css'
import './assets/startbootstrap-sb-admin-2-gh-pages/css/sb-admin-2.min.css'
import './assets/layout.css'
import './assets/customer-workspace.css'
import './style.css'
import 'leaflet/dist/leaflet.css'

cauHinhAxiosToanCuc()
kichHoatFetchBangAxios()

const app = createApp(App)

app.use(router)
app.mount("#app")
