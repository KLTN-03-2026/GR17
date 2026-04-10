<template>
    <div class="wrapper">
        <div class="header-wrapper">
            <TopKhachHang></TopKhachHang>
            <MenuKhachHang></MenuKhachHang>
        </div>
        <div class="page-wrapper">
            <div class="page-content">
                <router-view> </router-view>
            </div>
        </div>
        <BotKhachHang></BotKhachHang>
    </div>
</template>
<script>
import TopKhachHang from "../components/TopRocker.vue";
import BotKhachHang from "../components/BotRocker.vue";
import MenuKhachHang from "../components/MenuRocker.vue";


import "../../assets/js/bootstrap.bundle.min.js";
import "../../assets/js/jquery.min.js";
import "../../assets/plugins/simplebar/js/simplebar.min.js";
// import "../../assets/plugins/metismenu/js/metisMenu.min.js";
// import "../../assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js";
// import "../../assets/plugins/vectormap/jquery-jvectormap-2.0.2.min.js";
// import "../../assets/plugins/vectormap/jquery-jvectormap-world-mill-en.js";
import "../../assets/plugins/chartjs/js/Chart.min.js";
import "../../assets/plugins/chartjs/js/Chart.extension.js";
import "../../assets/js/index.js";
import "../../assets/js/app.js";


export default {
    name: "app",
    components: {
        TopKhachHang, MenuKhachHang, BotKhachHang
    }
}
</script>
<style>
/* @import"../../assets/images/favicon-32x32.png"; */
/* @import"../../assets/plugins/vectormap/jquery-jvectormap-2.0.2.css"; */
@import"../../assets/plugins/simplebar/css/simplebar.css";
@import"../../assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css";
@import"../../assets/plugins/metismenu/css/metisMenu.min.css";
@import"../../assets/css/pace.min.css";
/* @import"../../assets/js/pace.min.js"; */
@import"../../assets/css/bootstrap.min.css";
@import"../../assets/css/bootstrap-extended.css";
@import"../../assets/css/app.css";
@import"../../assets/css/icons.css";
@import"../../assets/css/dark-theme.css";
@import"../../assets/css/semi-dark.css";
@import"../../assets/css/header-colors.css";
@import url("https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap");
</style>