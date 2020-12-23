var category = new Vue({
  el: '#section',
  data: {
    postdata: [],
    siteurl: "",
  },
  methods: {
    getJson: async function(url) {
      const a = await axios({
        method: 'get',
        url: url,
        responseType: 'json',
      });
      return a.data;
    }
  },
  mounted: function() {
    this.siteurl = gamux.siteurl;
    this.$nextTick( async function() {
      this.postdata = await this.getJson(this.siteurl + '/wp-json/wp/v2/posts?per_page=20');
    })

  },
})