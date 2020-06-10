//扩展的localStorage方法
function Lstorage() {
    "use strict";
    var m = {
        set: function (key, value) {
            var item = {data: value};
            localStorage.setItem(key, JSON.stringify(item));
        },
        get: function (key) {
            var value = localStorage.getItem(key);
            if (!value) {
                return null;
            }
            value = JSON.parse(value);
            return value.data;
        },
        remove: function (key) {
            localStorage.removeItem(key);
            return null;
        },
        clear: function () {
            localStorage.clear();
        }
    };
    return m;
}

//百度地图相关方法的封装
function myMap() {
    "use strict";
    var m = {
        map: null,
        offsetY: 120,
        callback: null,
        localIcon: null,
        GetVer: -1,//获取版本号
        DragVer: 0,//拖动版本号
        location: null,//用户定位地点
        driversMarkers: [],//存储司机的marker数组
        init: function (container, offset, localIcon, callback) {
            if (offset) {
                this.offsetY = offset;
            }
            this.localIcon = localIcon;
            this.callback = callback;
            this.map = new BMap.Map(container);
            var point = new BMap.Point(116.404, 39.915);
            this.map.centerAndZoom(point, 15);
        },
        geolocation: function () {
            var geo = new BMap.Geolocation();
            var _this = this;
            geo.getCurrentPosition(function (res) {
                if (this.getStatus() === BMAP_STATUS_SUCCESS) {
                    //记录用户当前的位置
                    _this.location = res.point;
                    _this.map.panTo(res.point);
                    _this.geocoder(res.point);
                    //显示定位图标
                    _this.localIcon.show();
                    _this.locationMk(res.point);
                    //获得中心点的像素坐标
                    var centerPixel = _this.map.pointToOverlayPixel(_this.map.getCenter());
                    var newCenterPoint = _this.map.overlayPixelToPoint({
                        x: centerPixel.x,
                        y: centerPixel.y + _this.offsetY
                    });
                    _this.map.setCenter(newCenterPoint);
                    _this.move();
                }
            });
        },
        moveHandle: function (e) {
            var _this = this;
            _this.DragVer++;
            //获得当前center点
            var curCenterPixel = _this.map.pointToOverlayPixel(_this.map.getCenter());
            //获得定位图标所在位置并标点或者获取经纬度，解析地址
            var localIconPoint = _this.map.overlayPixelToPoint({
                x: curCenterPixel.x,
                y: curCenterPixel.y - _this.offsetY
            });
            _this.geocoder(localIconPoint);
        },
        listener : null,
        move: function () {
            var _this = this;
            var func = function(){
                _this.moveHandle();
            };
            _this.listener = func;
            _this.map.addEventListener('dragend', _this.listener);
        },
        dismove: function () {
            var _this = this;
            _this.map.removeEventListener('dragend',_this.listener);
        },
        geocoder: function (point) {//逆解析地址
            var _this = this;
            var mygeo = new BMap.Geocoder();
            mygeo.getLocation(point, function (result) {
                if (result) {
                    var title = null;
                    var pp = null;
                    if (result.surroundingPois.length) {
                        title = result.surroundingPois[0].title;
                        pp = result.surroundingPois[0].point;
                    } else {
                        title = result.addressComponents.street + result.addressComponents.streetNumber;
                        pp = result.point;
                    }
                    if (!title) {
                        title = result.address;
                    }
                    if (_this.callback) {
                        _this.callback({title: title, point: pp}, result);
                    }
                }
            });
        },
        locationMk: function (point) {//在地图上添加定位点
            var icon = new BMap.Icon('/frontend/web/cloudcar/images/location.png', new BMap.Size(14, 14));
            icon.setImageSize(new BMap.Size(14, 14));
            var lmk = new BMap.Marker(point, {icon: icon});
            this.map.addOverlay(lmk);
        },
        removeMk: function (mks) {
            for (var i = 0; i < mks.length; i++) {
                this.map.removeOverlay(mks[i]);
            }
        },
        driversMk: function (points) {
            var _this = this;
            if (!points.length) {
                return null;
            }
            if (_this.driversMarkers.length) {
                _this.removeMk(_this.driversMarkers);
                _this.driversMarkers = [];
            }
            //添加司机marker
            var icon = new BMap.Icon('/frontend/web/cloudcar/images/driver.png', new BMap.Size(28, 34));
            icon.setImageSize(new BMap.Size(28, 34));

            function addMarker(point) {
                var marker = new BMap.Marker(point, {icon: icon});
                _this.map.addOverlay(marker);
                _this.driversMarkers.push(marker);
            }

            var len = points.length;
            for (var i = 0; i < len; i++) {
                addMarker(points[i]);
            }
            // var bounds = _this.map.getBounds();
            // var sw = bounds.getSouthWest();
            // var ne = bounds.getNorthEast();
            // var lngSpan = Math.abs(sw.lng - ne.lng);
            // var latSpan = Math.abs(ne.lat - sw.lat);
            // for (var i = 0; i < 5; i ++) {
            //     var point = new BMap.Point(sw.lng + lngSpan * (Math.random() * 0.7), ne.lat - latSpan * (Math.random() * 0.7));
            //     addMarker(point);
            // }
        }
    };
    return m;
}