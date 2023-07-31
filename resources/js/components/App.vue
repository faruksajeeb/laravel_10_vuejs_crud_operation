<template>
  <div class="container">
    <Header />
    <div class="card">
      <div class="card-body">
        <div class="container-fluid">
          <router-view></router-view>
        </div>
      </div>
    </div>
    <Footer/>
  </div>
</template>
<script>
import Header from "./Header.vue";
import Footer from "./Footer.vue";
export default {
  name: "App",
  components: {Header,Footer},
};
</script>
