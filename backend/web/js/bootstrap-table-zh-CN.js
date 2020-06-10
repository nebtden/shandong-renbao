/**
 * Bootstrap Table Chinese translation
 * Author: Zhixin Wen<wenzhixin2010@gmail.com>
 */
(function ($) {
    'use strict';

    $.fn.bootstrapTable.locales['zh-CN'] = {
        formatLoadingMessage: function () {
            return '加载数据中，请稍候……';
        },
        formatRecordsPerPage: function (pageNumber) {
            return '每页显示 ' + pageNumber + ' 条记录';
        },
        formatShowingRows: function (pageFrom, pageTo, totalRows) {
            return '显示第 ' + pageFrom + ' 到第 ' + pageTo + ' 条记录，总共 ' + totalRows + ' 条记录';
        },
        formatSearch: function () {
            return '搜索';
        },
        formatNoMatches: function () {
            return '没有找到匹配的记录';
        },
        formatPaginationSwitch: function () {
            return '隐藏/显示分页';
        },
        formatRefresh: function () {
            return '刷新';
        },
        formatToggle: function () {
            return '切换';
        },
        formatColumns: function () {
            return '列';
        },
        formatMultipleSort: function() {
            return '多字段排序';
        },
        formatAddLevel: function() {
            return "添加字段";
        },
        formatDeleteLevel: function() {
            return "删除字段";
        },
        formatColumn: function() {
            return "字段";
        },
        formatOrder: function() {
            return "顺序";
        },
        formatSortBy: function() {
            return "主顺序";
        },
        formatThenBy: function() {
            return "次要顺序";
        },
        formatSort: function() {
            return "排序";
        },
        formatCancel: function() {
            return "取消";
        },
        formatDuplicateAlertTitle: function() {
            return "排序失败!";
        },
        formatDuplicateAlertDescription: function() {
            return "请删除多余排序字段";
        }
    };

    $.extend($.fn.bootstrapTable.defaults, $.fn.bootstrapTable.locales['zh-CN']);

})(jQuery);