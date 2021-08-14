/**
 * Carrega a imagem do captcha
 * 
 * @return {void}
 */
function carregaCaptcha() {
    let ajax = new XMLHttpRequest();
    ajax.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            let resultado = JSON.parse(this.responseText);
            let imagem = resultado.imagem;
            imprimeCaptcha(imagem);
        }
    };

    ajax.open("POST", "ajax.php", true);
    ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded")
    ajax.send("carregaCaptcha=1");
}

/**
 * Carrega o captcha na tela
 * 
 * @param {string} base64            Base64 da imagem
 * @return {void}
 */
function imprimeCaptcha(base64) {
    document.getElementById("img_captcha").src = base64;
}