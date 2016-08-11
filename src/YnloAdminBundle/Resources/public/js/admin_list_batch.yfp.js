/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

YnloFramework.AdminListBatch = {
    config: {},
    init: function () {
        YnloFramework.AdminListBatch.initCheckboxes();
        YnloFramework.AdminListBatch.initActionListener();
    },
    initActionListener: function (action) {
        $(document).on('click', '[data-batch-action]', function () {
            var input = $('<input type="hidden" name="action">');
            input.val($(this).data('batch-action'));
            $(this).parents('form').append(input).submit();
        });
    },
    initCheckboxes: function () {
        $(document).on('change ifChanged', '#list_batch_checkbox', function () {
            var e = $(this)
                .closest('table')
                .find('td.sonata-ba-list-field-batch input[type="checkbox"], div.sonata-ba-list-field-batch input[type="checkbox"]');

            if (e.iCheck !== undefined) {
                e.iCheck($(this).is(':checked') ? 'check' : 'uncheck')
            } else {
                e.prop('checked', $(this).is(':checked') ? true : false);
            }
        });
        $(document)
            .on('change ifChanged', 'td.sonata-ba-list-field-batch input[type="checkbox"], div.sonata-ba-list-field-batch input[type="checkbox"]', function () {
                $(this)
                    .closest('tr, div.sonata-ba-list-field-batch')
                    .toggleClass('active sonata-ba-list-row-selected', $(this).is(':checked'));
            })
            .trigger('change ifChanged');
    }
};