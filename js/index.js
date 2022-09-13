var index = new Vue({
    el: '#section',
    data: {
        slidedata: [],
        carHeight: 0,
        wishlist: null,
        postnum: 10,
        postdata: [],
        caturl: "",
        siteurl: "",
        cateidlist: [],
        steamid: 0,
        gamecount: 0,
    },
    methods: {
        createVisible: function() {
            if (gamux.islogin == 1) {
                gamux.createVisible = true;
            } else {
                gamux.$refs.login.$el.click();
            }
        },
        getCarHeight: function() {
            return this.$refs.carcol1.$el.clientHeight;
        },
        getWishList: async function() {
            var graph = graphql("https://api.github.com/graphql", {
                headers: {
                    "Authorization": "Bearer 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",
                    "Accept": "application/vnd.github.v4.idl",
                    "GraphQL-Features": "discussions_api"
                },
                asJSON: true
            });
            const gqls = graph(`query {
                repository(name: "bbs", owner: "Gamuxorg") {
                    discussions(first: 10, categoryId: "DIC_kwDOBm0DV84CPnJz") {
                        nodes {
                            url
                            title
                        }
                    }
                }
            }`);
            const githubdiscussion =  await gqls();
            return githubdiscussion.repository.discussions.nodes;
        },
        getCatJson: async function() {
            const a = await axios({
                method: 'get',
                url: this.siteurl + '/wp-json/wp/v2/categories',
                params: {
                    "parent": 256,
                    "per_page": 100,
                },
            });
            return a.data;
        },
        getCatGamesJson: async function() {
            const a = await axios({
                method: 'get',
                url: this.siteurl + '/wp-json/gamux/v1/categories/256',
            });
            return a.data;
        },
        getJsonComm: async function(url) {
            const a = await axios({
                method: 'get',
                url: url,
                params: {
                    "per_page": 12,
                    "page": 1,
                    "categories": this.cateidlist,
                }
            });
            return a.data;
        }
    },
    mounted: async function() {
        this.siteurl = gamux.siteurl;
        this.$nextTick(function() {
            this.carHeight = this.getCarHeight();
        });
        //游戏数量
        const cateGames = await this.getCatGamesJson();
        this.gamecount = cateGames.count;
        //首页轮播
        const cate = await this.getCatJson();
        for (var i = 0; i < cate.length; i++) {
            this.cateidlist[i] = cate[i]["id"];
        }
        const slidedatas = await this.getJsonComm(this.siteurl + '/wp-json/gamux/v1/images/mainslide/4');
        this.slidedata = slidedatas.data;
        //游戏上新
        this.postdata = await this.getJsonComm(this.siteurl + 'wp-json/wp/v2/posts');
        //需求清单
        const wishlistdata = await this.getWishList();
        console.log(wishlistdata);
        this.wishlist = wishlistdata;
        //文章高度与轮播高度一致
        const that = this;
        window.onresize = function() {
            calTime1 = setTimeout(function() {
                that.carHeight = that.getCarHeight();
            }, 500);
        };
    },
    watch: {
        carHeight: function(val) {
            this.carHeight = val;
        },
    },
    beforeDestory: function() {
        clearTimeout(calTime1);
    }
})