var comOne = {
  template: `
      <div class="select-box">
        <ul class="s-list-one">
          <li
            v-for="(v2,i2) in v.option"
            :key="i2"
            :class="{'active': v.select.toString() == v2}"
            @click="oneSelectClick(v,v2,i,i2)"
          >{{ v2 }}</li>
        </ul>
      </div>
    `,
  props: {
    list: {
      type: Array,
      require: true,
    },
    v: {
      type: Object,
      require: true
    },
    i: {
      type: Number,
      require: true
    },
  },
  methods: {
    oneSelectClick: function (v,v2,i,i2) {
      // 初始选择
      if (v.select.length === 0) {
        v.selectIndex = [i2];
        v.select = [v2];
      } else if ( v.select[0] === v2 ) {
        // 编辑，选择之前选择的
        this.list[i].toggleSelect = false;
        this.list[i].toggleEdit = true;
        return;
      } else {
        // 编辑，另外的选择
        v.selectIndex = [i2];
        v.select = [v2];
        this.$emit('set-index', { i });
        this.$emit('reset-list-toggle', { i })
      }
      this.list[i].toggleSelect = false;
      this.list[i].toggleEdit = true;
      if (v.getScore) {
        v.score = v.getScore(v);
      }
      this.$emit('to-next');
    },
  }
};

var comMulti = {
  template: `
      <div class="select-box select-box-more">
        <span class="title-more">可多选</span>
        <ul class="s-list-more">
          <li
            v-for="(v2,i2) in v.option"
            :key="i2"
            :class="{'active': v.selectIndex.indexOf(i2)>=0}"
            @click="multiSelectClick(v,v2,i,i2)"
          >
            <div class="checkbox-like"></div>
            <div class="checkbox-text">{{ v2 }}</div>
          </li>
        </ul>
        <span class="btn-confirm" @click="multiSelectConfirm(i)">继续</span>
      </div>
    `,
  props: {
    index: {
      type: Number,
      require: true,
    },
    list: {
      type: Array,
      require: true,
    },
    v: {
      type: Object,
      require: true
    },
    i: {
      type: Number,
      require: true
    },
  },
  methods: {
    multiSelectClick: function(v,v2,i,i2) {
      var findIndex = v.select.findIndex(function (fv) {
        return fv === v2;
      });
      // 没有则添加，存在则取消
      if (findIndex === -1) {
        v.selectIndex.push(i2);
      } else {
        v.selectIndex.splice(findIndex, 1)
      }
      v.selectIndex.sort();
      formatIndexToVal(v);
    },
    multiSelectConfirm: function(i) {
      this.$emit('set-index', {i});
      this.$emit('reset-list-toggle', {i});
      this.list[this.index].toggleSelect = false;
      this.list[this.index].toggleEdit = true;
      this.$emit('to-next');
    },
  }
};

var comSlide = {
  template: `
      <div class="select-box select-box-slide">
          <div class="range-limit" :id="container">
            <input type="hidden" :id="picker">
          </div>
          <span class="btn-confirm" @click="toConfirm">继续</span>
        </div>
    `,
  props: {
    index: {
      type: Number,
      require: true,
    },
    list: {
      type: Array,
      require: true,
    },
    v: {
      type: Object,
      require: true
    },
    i: {
      type: Number,
      require: true
    },
  },
  data() {
    return {
      toggleShow: false,
      container: 'id' + parseInt(Math.random() * 10000 ),
      picker: 'id' + parseInt(Math.random() * 10000 )
    }
  },
  methods: {
    initShow: function() {
      if (this.toggleShow) {
        return false;
      }
      this.toggleShow = true;
      var cols = [];
      if (Array.isArray(this.v.option[0])) {
        this.v.option.forEach(function (tv) {
          cols.push({
            textAlign: 'center',
            values: tv
          })
        });
      }
      var tmp = $('#' + this.picker).picker({
        container: '#' + this.container,
        title: "",
        cols
      });

    },
    toConfirm: function() {
      var _this = this;
      var val = $('#' + this.picker).val();
      var arr = val.split(' '); // ['2000年','1月']
      var option = this.v.option;
      var select = [];
      var selectIndex = [];
      arr.forEach(function (tv, ti) {
        select.push(tv);
        var index = option[ti].findIndex(function (tv2,ti2) {
          return tv === tv2
        })
        selectIndex.push(index);
      });
      this.v.select = select;
      this.v.selectIndex = selectIndex;
      var score = parseInt(arr[0]);
      if (this.v.getScore) {
        this.v.score = this.v.getScore(score);
      }
      this.$emit('set-index', {i: this.i});
      this.$emit('reset-list-toggle', {i: this.i});
      this.list[this.index].toggleSelect = false;
      this.list[this.index].toggleEdit = true;
      this.$emit('to-next');
    },
  }
};

var comSlideMore = {
  template: `
      <div class="select-box select-box-more-slide">
          <ul class="s-list">
            <li v-for="(v2,i2) in v.list" :class="{'active': v2.toggle}">
              <div class="control-box">
                <span>{{ v2.title }}</span>
                <span class="btn-control" @click="clickSpread(i2)">
                  请选择
                </span>
              </div>
              <div class="detail-box">
                <div class="limit" :id="v2.container">
                  <input type="hidden" :id="v2.picker">
                </div>
              </div>
            </li>
          </ul>
          <span class="btn-confirm" @click="toConfirm">继续</span>
        </div>
    `,
  props: {
    index: {
      type: Number,
      require: true,
    },
    list: {
      type: Array,
      require: true,
    },
    v: {
      type: Object,
      require: true
    },
    i: {
      type: Number,
      require: true
    },
  },
  data() {
    return {
      toggleShow: false
    }
  },
  methods: {
    initShow: function() {
      if (this.toggleShow) {
        return false;
      }
      this.toggleShow = true;
      var slist = this.v.list;
      slist.forEach(function (tv, ti) {
        $('#' + tv.picker).picker({
          container: '#' + tv.container,
          title: "",
          cols: [
            {
              textAlign: 'center',
              values: tv.option
            }
          ]
        });
        if (ti > 0) {
          tv.toggle = false
        }
      });
    },
    clickSpread: function(i2) {
      this.v.list.forEach(function (tv, ti) {
        tv.toggle = false;
        if (ti === i2) {
          tv.toggle = true;
        }
      })
    },
    toConfirm: function() {
      var select = [];
      this.v.list.forEach(function (tv, ti) {
        var val = $('#' + tv.picker).val();
        select.push(val);
      });
      this.v.select = select;
      this.$emit('set-index', {i: this.i});
      this.$emit('reset-list-toggle', {i: this.i});
      this.list[this.index].toggleSelect = false;
      this.list[this.index].toggleEdit = true;
      this.$emit('to-next');
    },
  }
};

var comOneMore = {
  template: `
      <div class="select-box select-box-more-one">
          <ul class="s-list">
            <li v-for="(v2,i2) in v.list" :key="i2" :class="{'active': v2.toggle}" >
              <div class="control-box">
                <span>{{ v2.title }}</span>
                <span class="btn-control" @click="clickSpread(i2)">
                  请选择
                </span>
              </div>
              <div class="detail-box">
                <ul class="s-list-more">
                  <li
                    v-for="(v3,i3) in v2.option"
                    :key="i3"
                    :class="{'active': v3 == v2.select.toString()}"
                    @click="selectOne(v2,v3,i3)"
                  >
                    <div class="checkbox-like"></div>
                    <div class="checkbox-text">{{ v3 }}</div>
                  </li>
                </ul>
              </div>
            </li>
          </ul>
          <span class="btn-confirm" @click="toConfirm">继续</span>
        </div>
    `,
  props: {
    index: {
      type: Number,
      require: true,
    },
    list: {
      type: Array,
      require: true,
    },
    v: {
      type: Object,
      require: true
    },
    i: {
      type: Number,
      require: true
    },
  },
  data() {
    return {
      toggleShow: false
    }
  },
  methods: {
    initShow: function() {
      if (this.toggleShow) {
        return false;
      }
      this.toggleShow = true;
      var slist = this.v.list;
      slist.forEach(function (tv, ti) {
        $('#' + tv.picker).picker({
          container: '#' + tv.container,
          title: "",
          cols: [
            {
              textAlign: 'center',
              values: tv.option
            }
          ]
        });
        if (ti > 0) {
          tv.toggle = false
        }
      });
    },
    clickSpread: function(i2) {
      this.v.list.forEach(function (tv, ti) {
        tv.toggle = false;
        if (ti === i2) {
          tv.toggle = true;
        }
      })
    },
    selectOne: function(v2,v3,i3) {
      v2.select = [v3];
      v2.selectIndex = [i3];
    },
    toConfirm: function() {
      for (var j = 0, len = this.v.list.length; j<len; j++) {
        var obj = this.v.list[j];
        if (obj.select.length===0) {
          return false;
        }
      }
      var select = [];
      this.v.list.forEach(function (fv) {
        select.push(fv.select[0]);
      });
      this.v.select = select;
      if (this.v.getScore) {
        this.v.score = this.v.getScore(this.v);
      }
      this.$emit('set-index', {i: this.i});
      this.$emit('reset-list-toggle', {i: this.i});
      this.list[this.index].toggleSelect = false;
      this.list[this.index].toggleEdit = true;
      this.$emit('to-next');
    },
  }
};


