var gamux = new Vue ({
  el: '#container',
  data: {
    headersearch: '',
    display: '',
    sectionMarginLeft: ''
  },
  methods: {
    getClientWidth() {
      return document.body.clientWidth;
    }
  },
  mounted() {
    this.display = this.getClientWidth() > 768 ? 'block':'none';
    this.sectionMarginLeft = this.getClientWidth() > 768 ? 20:0;
    var that = this;
    window.onresize=()=>{
      var b = document.body.clientWidth;
      if (b > 768) {
        that.sectionMarginLeft = 20;
        that.display = 'block';  
      }
      else {
        that.sectionMarginLeft = 0;
        that.display = 'none';
      }
    }
  }  
})
