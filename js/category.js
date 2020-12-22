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
  mounted: async function() {
    this.siteurl = gamux.siteurl;
    const postdatas = await this.getJson(this.siteurl + '/wp-json/wp/v2/posts?per_page=20');
    for(i=0;i<postdatas.length;i++) {
      this.postdata[i] = postdatas[i];
      const a = this.postdata[i]["modified"];
      const b = a.split("T");
      const c = b[0];
      this.postdata[i]["modified"] = c;
    }
  },
})