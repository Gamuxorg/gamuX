var index = new Vue({
  el: '#section',
  data: {
    items: [
      {src: "https://media.st.dl.pinyuncloud.com/steam/apps/458710/ss_b54a3709226a6a5971c66a0c102eb45d46cff9e5.600x338.jpg?t=1560522460"},
      {src: "https://media.st.dl.pinyuncloud.com/steam/apps/458710/ss_d5303784b13da07936e3c8e9121b3d286eda8b9c.600x338.jpg?t=1560522460"},
      {src: "https://media.st.dl.pinyuncloud.com/steam/apps/458710/ss_76881eb3fdb62c63ae71ec2b6737ce8321ffabe0.600x338.jpg?t=1560522460"}
    ],
    carHeight: 0,
  },
  methods: {
    getCarUrl: function(url) {
      return url;
    },
    getCarHeight: function() {
      return this.$refs.carcol1.$el.clientHeight;
    }
  },
  mounted: function(){
    this.$nextTick(function(){
      this.carHeight = this.getCarHeight();
    });
    const that = this;
    window.onresize = function(){
      calTime1 = setTimeout(function(){
        that.carHeight = that.getCarHeight();
      }, 500);
    };
  },
  watch: {
    carHeight: function(val){
      this.carHeight = val;
    },
  },
  beforeDestory: function() {
    clearTimeout(calTime1);
  }
})

