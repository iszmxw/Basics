/**
 * 类似PHP的分割数组函数
 * @param separator
 * @param str
 * @returns {Element|ChildNode|string[]|*|{}}
 */
function explode(separator, str) {
    return str.split(separator);
}