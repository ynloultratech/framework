
//Functions
String.prototype.toCamelCase = function () {
    return this.replace(/[^A-Za-z0-9]/g, ' ').replace(/^\w|[A-Z]|\b\w|\s+/g, function (match, index) {
        if (+match === 0 || match === '-' || match === '.') {
            return ""; // or if (/\s+/.test(match)) for white spaces
        }
        return index === 0 ? match.toLowerCase() : match.toUpperCase();
    });
};

function randomString(length) {
    var chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz'.split('');

    if (!length) {
        length = Math.floor(Math.random() * chars.length);
    }

    var str = '';
    for (var i = 0; i < length; i++) {
        str += chars[Math.floor(Math.random() * chars.length)];
    }
    return str;
}

function log(msg) {
    YnloFramework.log(msg);
}