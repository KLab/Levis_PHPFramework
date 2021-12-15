
var getDataTableOptions = function(data, columns)
{
    return {
        "data": data,
        "paging":true,
        "aLengthMenu": [
            [25,50,75,100,200,300,-1],//表示行数
            [25,50,75,100,200,300,"All"]//ページャーのセレクタ表示文字列
        ],
        "columns": columns
    };
}
