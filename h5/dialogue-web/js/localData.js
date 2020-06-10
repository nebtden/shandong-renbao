function getYearArr() {
  var arr = [];
  var start = 1900;
  var end = new Date().getFullYear();
  while(start <= end) {
    arr.push(start + '年');
    start += 1;
  }
  return arr;
}
function getMonthArr() {
  var arr = [];
  var start = 1;
  var end = 12;
  while(start <= end) {
    arr.push(start + '月');
    start += 1;
  }
  return arr;
}
function getRangeNumber(start = 10, end = 300) {
  var arr = [];
  while(start <= end) {
    arr.push(start);
    start += 1;
  }
  return arr;
}
var list = [
  {
    type: 'desc',
    description: '本评估根据世界上最著名的睡眠质量评估表之一《匹兹堡睡眠指数量表》开发而来，该量表具有较好的信度、效度，81%的用户表示本测评结果对ta有帮助。'
  },
  {
    type: 'desc',
    description: '评估大约需要3分钟左右，您准备好了吗？'
  },
  {
    type: 'slide',
    description: '年龄是健康建议的重要参考，请问您的出生年月是？',
    option: [getYearArr(), getMonthArr()],
    score: 0,
    getScore: function (val) {
      if (val>=1990 && val<2000) {
        return 0;
      } else if (val>=1970 && val<1980) {
        return 2;
      } else if (val>=1960 && val<1970) {
        return 5;
      }
      return 0;
    }
  },
  {
    type: 'one',
    description: '不同的性别睡眠质量不同，请问您的性别是？',
    option: [
      '男', '女'
    ],
    score: 2,
    getScore: function (obj) {
      var index = obj.selectIndex[0];
      return [2, 1][index];
    }
  },
  {
    type: 'slideMore',
    description: '是否肥胖对睡眠质量影响比较大，请问您的身高、体重是？',
    option: [],
    list: [
      {
        container: (function () {
          return 'id' + parseInt(Math.random() * 10000 )
        })(),
        picker: (function () {
          return 'id' + parseInt(Math.random() * 10000 )
        })(),
        toggle: true,
        title: '身高(厘米)',
        option: getRangeNumber(100, 200)
      },
      {
        container: (function () {
          return 'id' + parseInt(Math.random() * 10000 )
        })(),
        picker: (function () {
          return 'id' + parseInt(Math.random() * 10000 )
        })(),
        toggle: true,
        title: '体重(公斤)',
        option: getRangeNumber(20, 200)
      }
    ],
    score: 2,
    getScore: function () {
      return 2;
    }
  },
  {
    spec: '睡眠障碍多',
    type: 'one',
    description: '相比已婚，未婚的人睡眠质量更佳。请问您的婚姻状况如何？',
    option: [
      '单身', '未婚有伴侣', '已婚', '离婚', '丧偶'
    ],
    score: 0,
    getScore: function (obj) {
      var index = obj.selectIndex[0];
      return [0, 1, 2, 3, 5][index];
    }
  },
  {
    type: 'one',
    description: '不少行业压力较大，总是睡不好，您从事的行业是？',
    option: [
      '互联网从业人员', '金融从业人员', '医护人员', '媒体人', '公务员', '教师', '广告/公关人', '家庭主妇', '企业主', '服务、零售行业', '工人', '农民', '学生', '其他'
    ],
    score: 0,
    getScore: function (obj) {
      // var index = obj.selectIndex[0];
      // return [0, 1, 2, 3, 5][index];
      return 5;
    }
  },
  {
    type: 'one',
    description: '总体而言年收入与睡眠关系呈现U型分布，收入较低及较高群体更容易有困扰。请问您的年收入是？',
    option: [
      '0~1万', '2~4万', '5~10万', '11~20万', '21~40万', '41~80万', '超过80万'
    ],
    score: 0,
    getScore: function (obj) {
      var index = obj.selectIndex[0];
      return [5, 5, 3, 2, 4, 5, 5][index];
    }
  },
  {
    type: 'one',
    description: '学历高的人“睡商”并不一定高，请问您的最高学历是？',
    option: [
      '小学', '中学', '高中及同等学力', '大学专科', '大学本科', '硕士研究生', '博士研究生'
    ],
    score: 0,
    getScore: function (obj) {
      var index = obj.selectIndex[0];
      return [2, 3, 5, 4, 5, 5, 5][index];
    }
  },
  {
    type: 'one',
    description: '近一个月，您晚上通常几点上床睡觉（无论睡着与否）？',
    option: [
      '10点前', '10点~11', '11点~0点', '0点以后'
    ],
    score: 0,
    getScore: function (obj) {
      var index = obj.selectIndex[0];
      return [0, 2, 3, 5][index];
    }
  },
  {
    spec: '入睡时间长',
    type: 'one',
    description: '近一个月，您入睡通常需要多少分钟？',
    option: [
      '15分钟以内', '16~30分钟', '31~60分钟', '1~2小时', '2小时以上 '
    ],
    score: 0,
    getScore: function (obj) {
      var index = obj.selectIndex[0];
      return [0, 1, 3, 4, 5][index];
    }
  },
  {
    spec: '入睡时间长',
    type: 'one',
    description: '您入睡困难（30分钟内不能入睡）的频率如何？',
    option: [
      '无', '每周少于1次', '每周1~2次', '每周大于3次'
    ],
    score: 0,
    getScore: function (obj) {
      var index = obj.selectIndex[0];
      return [0, 1, 2, 5][index];
    }
  },
  {
    spec: '睡眠时间短',
    type: 'one',
    description: '近1个月，您早上通常几点钟起床？（起身才算，睡醒了躺着不算起床）',
    option: [
      '7点以前', '7~8点', '8点~9点', '9点以后'
    ],
    score: 0,
    getScore: function (obj) {
      var index = obj.selectIndex[0];
      return [1, 0, 2, 4][index];
    }
  },
  {
    spec: '睡眠时间短',
    type: 'one',
    description: '平均来说，您每天可以睡着的时间长度，有多长时间（几小时几分钟）？',
    option: [
      '6小时以内', '7~8小时', '8~9小时', '9小时以上'
    ],
    score: 0,
    getScore: function (obj) {
      var index = obj.selectIndex[0];
      return [4, 2, 0, 5][index];
    }
  },
  {
    spec: '睡眠效率低',
    type: 'one',
    description: '睡眠不好常常会表现为夜间醒来，或者凌晨很早睡醒，您有这样的情况吗？每周会发生几次呢？',
    option: [
      '无', '每周小于1次', '每周1~2次', '每周大于3次'
    ],
    score: 0,
    getScore: function (obj) {
      var index = obj.selectIndex[0];
      return [0, 1, 2, 4][index];
    }
  },
  {
    spec: '睡眠障碍多',
    type: 'oneMore',
    description: '近1个月 ，因下列情况影响睡眠而烦恼吗？',
    option: [],
    list: [
      {
        toggle: true,
        title: '呼吸不畅',
        select: [],
        selectIndex: [],
        option: [
          '无','每周少于1次','每周1-2次','每周大于3次'
        ]
      },
      {
        toggle: false,
        title: '咳嗽或鼾声高',
        select: [],
        selectIndex: [],
        option: [
          '无','每周少于1次','每周1-2次','每周大于3次'
        ]
      },
      {
        toggle: false,
        title: '感觉冷',
        select: [],
        selectIndex: [],
        option: [
          '无','每周少于1次','每周1-2次','每周大于3次'
        ]
      },
      {
        toggle: false,
        title: '做恶梦',
        select: [],
        selectIndex: [],
        option: [
          '无','每周少于1次','每周1-2次','每周大于3次'
        ]
      },
      {
        toggle: false,
        title: '疼痛不适',
        select: [],
        selectIndex: [],
        option: [
          '无','每周少于1次','每周1-2次','每周大于3次'
        ]
      }
    ],
    score: [],
    getScore: function (obj) {
      var arr = [0,1,3,5];
      var score = []
      obj.list.forEach(function (v) {
        score.push(arr[v.selectIndex[0]])
      })
      return score;
    }
  },
  {
    spec: '用药频率高',
    type: 'one',
    description: '您是否需要用药物才能入睡？',
    option: [
      '无', '每周小于1次', '每周1~2次', '每周大于3次'
    ],
    score: 0,
    getScore: function (obj) {
      var index = obj.selectIndex[0];
      return [0, 2, 4, 5][index];
    }
  },
  {
    spec: '白天精力差',
    type: 'one',
    description: '近一个月，时常感觉精力不足',
    option: [
      '经常', '有时', '很少', '从未'
    ],
    score: 0,
    getScore: function (obj) {
      var index = obj.selectIndex[0];
      return [5, 3, 1, 0][index];
    }
  },
  {
    spec: '白天精力差',
    type: 'one',
    description: '近一个月，总的来说，您认为自己的睡眠质量',
    option: [
      '很差', '较差', '较好', '很好'
    ],
    score: 0,
    getScore: function (obj) {
      var index = obj.selectIndex[0];
      return [5, 3, 1, 0][index];
    }
  }
];
function addRef(list) {
  list.forEach(function (v, i) {
    v.ref = 'ref' + i;
  })
}
addRef(list);
// 增加初始判断参数,重置list状态
function resetListToggle(list, start = -1) {
  list.forEach(function (v, i) {
    if (i > start) {
      v.selectIndex = [];
      v.select = [];
      v.toggleSpinner = true;
      v.toggleSelect = false;
      v.toggleEdit = false;
    }
  })
}
resetListToggle(list);
// 格式化数据，根据selectIndex映射select的值
function formatIndexToVal(obj) {
  var option = obj.option;
  var select = [];
  var selectIndex = obj.selectIndex;
  selectIndex.forEach(function (v, i) {
    select.push(option[v])
  });
  obj.select = select;
}
