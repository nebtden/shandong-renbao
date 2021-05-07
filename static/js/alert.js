/**
 * Created by Administrator on 2016/2/25.
 */
(function( $ ){
    $.fn.extend({
        tanchu : ( function () {
            var singleton = function ( fn ) {
                var result;
                return function () {
                    return result || ( result = fn.apply( this , arguments ) );
                }
            }

            var factory = singleton ( function ( tanchu ) {
                var div = document.createElement( 'div' );
                    div.id = tanchu;
                    div.innerHTML = '<span id="tan_sp"></span><em><i id="tan_i"></i>s后关闭</em>';
                    div.style.display = 'none';
                    return document.body.appendChild( div );
            });

            var element = factory( 'tanchu' ), flag = true;

            return function ( text , time ) {
                if( !flag ){return false}
                var _$,
                    tan_sp,
                    tan_i,
                    getBoundRect,
                    scrollHeight,
                    windowPix,
                    widthBan,
                    heightBan,
                    counter,
                    setCss,
                    animateStart,
                    animateStop,
                    animateTop,
                    animateBot,
                    doFor,
                    compareBig,
                    compareSmall,
                    setIntervalFun;

                time = time || 2;

                counter = 0;

                _$ = function ( id ) {
                    return document.getElementById( id );
                };

                tan_sp = _$( 'tan_sp' );

                tan_i = _$( 'tan_i' );

                getBoundRect = ( function () {
                    tan_sp.innerHTML = text;
                    tan_i.textContent = time;
                    function getType( o ){
                        var _t;
                        return (
                            ( _t = typeof( o ) ) == "object" ?
                                  o == null && "null" ||
                                  Object.prototype.toString.call( o ).slice( 8 ,-1 ):_t
                            ).toLowerCase();
                    }
                    function getStyle( el , styleName ) {
                        return el.style[ styleName ] ?
                               el.style[ styleName ] :
                               el.currentStyle ? el.currentStyle[ styleName ] : window.getComputedStyle( el , null )[ styleName ];
                    }

                    function getStyleNum( el , styleName ) {
                        return parseInt( getStyle( el , styleName ).replace( /px|pt|em/ig,'' ) );
                    }

                    function setStyle( el , obj ){
                        if ( getType( obj ) == "object" ) {
                            for ( var p in obj ) {
                                el.style[ p ] = obj[ p ];
                            }
                        }
                        else
                        if ( getType( obj ) == "string" ) {
                            el.style.cssText = obj;
                        }
                    }

                    function getSize( el ) {
                        var _addCss = {
                            display: "",
                            position: "absolute",
                            visibility: 'hidden'
                        };
                        var _oldCss = {};
                        for ( var i in _addCss ) {
                            _oldCss[ i ] = getStyle( el , i );
                        }
                        setStyle( el , _addCss );
                        var _width = el.clientWidth || getStyleNum( el , "width" );
                        var _height = el.clientHeight || getStyleNum( el , "height" );
                        setStyle( el , _oldCss );
                        return { width: _width, height: _height };
                    }
                    return getSize( element )
                })();

                scrollHeight = ( function () {
                    return document.body.scrollTop || document.documentElement.scrollTop;
                } )();

                windowPix = ( function () {
                    return {
                        width : document.documentElement.clientWidth || document.body.clientWidth,
                        height: document.documentElement.clientHeight || document.body.clientHeight
                    };
                } )();

                widthBan  = ( windowPix.width  - getBoundRect.width ) / 2;
                //scrollHeight加上滚动条滚动高度才能在有滚动条的情况下始终居屏幕中间
                heightBan = ( windowPix.height - getBoundRect.height ) / 2 + scrollHeight;

                setCss = function () {
                    var _shift = Array.prototype.shift;
                    this.style.opacity = _shift.call( arguments );
                    this.style.top     = _shift.call( arguments ) + _shift.call( arguments ) + 'px';
                    this.style.left    = _shift.call( arguments ) + 'px';
                }

                //比较位置
                compareSmall = function () {
                    var top = parseInt( this.style.top );
                    if( top <= heightBan ){
                        return true;
                    }
                    return false;
                }

                compareBig = function () {
                    var top = parseInt( this.style.top );
                    if( top >= heightBan + 20 ){
                        return true;
                    }
                    return false;
                }

                //定义基本位置信息，透明度，及显示时间
                animateStart = function () {
                    setCss.apply( this , [ 0 , heightBan , 20 , widthBan ] );
                    this.style.display = 'block';
                };

                animateStop = function () {
                    flag = true;
                    setCss.apply( this , [ 0 , heightBan , 0 , widthBan ] );
                    this.style.display = 'none';
                };

                animateTop = function () {
                    flag = false;
                    animateStart.call( this );
                    setIntervalFun.call( this , compareSmall , doFor );
                }

                animateBot = function () {
                    setIntervalFun.call( this , compareBig , animateStop )
                }

                doFor = function () {
                    var setTimer = setInterval( function () {
                        tan_i.textContent = parseInt( tan_i.textContent ) - 1;
                        counter++;
                        if( counter >= time - 1 ){
                            clearInterval( setTimer );
                            var timer = setTimeout( function () {
                                animateBot.call( this );
                                clearTimeout( timer );
                            }.bind( this ) , 500 );
                        }
                    }.bind( this ) , 1000 );
                }

                var setIntervalFun = function( fn1 , fn2 ) {
                    var setTimer = setInterval( function () {
                        var top = parseInt( this.style.top );
                        var opacity = parseFloat( this.style.opacity );
                        if( fn1 == compareSmall ){
                            top -= 2;
                            opacity += 0.1;
                            opacity = opacity > 1 ? 1 : opacity;
                        }
                        if( fn1 == compareBig ){
                            top += 2;
                            opacity -= 0.1;
                            opacity = opacity < 0 ? 0 : opacity;
                        }
                        if( fn1.call( this ) ){
                            clearInterval( setTimer );
                            fn2.call( this );
                        }
                        this.style.top = top + 'px';
                        this.style.opacity = opacity
                    }.bind( this ) , 25 );
                }

                animateTop.call( element );
            }
        })()
    });
}( jQuery ));