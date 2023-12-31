export const OK = 200;
export const CREATED = 201;
export const BAD_REQUEST = 400;
export const UNAUTHORIZED = 401;
export const NOT_FOUND = 404;
export const UNAUTHORIZED_2 = 419;
export const UNPROCESSABLE_ENTITY = 422;
export const INTERNAL_SERVER_ERROR = 500;

// クッキーの値を取得する関数
export function getCookieValue() {
    return document.cookie.split("=")[1];
}
