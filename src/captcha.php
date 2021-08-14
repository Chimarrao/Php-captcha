<?php

class captcha {
    /**
     * @var string $fonte           Arquivo de fonte
     */
    public string $fonte            = "arial.ttf";

    /**
     * @var string $cor_fundo       Cor do fundo em hexadecimal
     */
    public string $cor_fundo        = "#000000";

    /**
     * @var string $cor_fonte       Cor da fonte em hexadecimal
     */
    public string $cor_fonte        = "#ffffff";

    /**
     * @var string $char_set        Conjunto de dígidos para criação do captcha
     */
    public string $char_set         = "ABCDEFGHIJKLMNPQRSTUVYXWZabcdefghijklmnpqrstuvyxwz23456789";

    /**
     * @var string $ultimo_texto    Último texto gerado em captcha
     */
    public string $ultimo_texto     = "";

    /**
     * @var bool $base64            Se True, retorna em Base64 a imagem gerada de captcha
     */
    public bool $base64             = False;
    
    /**
     * Gera e retorna um captcha
     *
     * @param  int $largura         Largura da imagem do captcha
     * @param  int $altura          Altura da imagem do captcha
     * @param  int $qtd_letras      Quantidade de letras do captcha
     * @param  int $tamanho_fonte   Tamanho da fonte do captcha
     * @return GdImage|string|false Imagem, string em Base64 do Captcha ou False em caso de erro
     */
    public function captcha(int $largura, int $altura, int $qtd_letras, int $tamanho_fonte, string $formato = "jpeg") {
        if ($largura <= 0 || $altura <= 0 || $qtd_letras <= 0 || $tamanho_fonte <= 0) {
            return False;
        }

        ob_start();
        
        $this->ultimo_texto         = $this->gerarTexto($qtd_letras);
        $imagem                     = imagecreatetruecolor($largura, $altura);
        
        extract($this->hexToRGB($this->cor_fundo));
        imagecolorallocate($imagem, $r, $g, $b);
        
        extract($this->hexToRGB($this->cor_fonte));
        $cor_fonte                  = imagecolorallocate($imagem, $r, $g, $b);

        $imagem                     = $this->escreveCaptcha($imagem, $this->ultimo_texto, $tamanho_fonte, $cor_fonte);
        $formato                    = strtolower($formato);

        match ($formato) {
            "gif"                   => imagegif(image: $imagem),
            "png"                   => imagepng(image: $imagem, quality: 0),
            "webp"                  => imagewebp(image: $imagem, quality: 100),
            "bitmap"                => imagebmp(image: $imagem, compressed: False),
            "jpeg"                  => imagejpeg(image: $imagem, quality: 100),
            default                 => imagejpeg(image: $imagem, quality: 100),
        };

        ob_end_clean(); 
        
        if ($this->base64) {
            return $this->GDImageToBase64($imagem, $formato);
        } 

        return $imagem;
    }
    
    /**
     * Converte uma cor hexadecimal para RGB
     *
     * @param  string $rgb          Cor em hexadecimal
     * @return array                Array com RGB das cores
     */
    private function hexToRGB(string $cor) {
        if ($cor[0] == "#") {
            $cor                    = substr($cor, 1);
        }
        
        list($r, $g, $b)            = array_map("hexdec", str_split($cor, (strlen($cor) / 3)));
        
        return array (
            "r"                     => $r, 
            "g"                     => $g, 
            "b"                     => $b
        );
    }
    
    /**
     * Gera um texto aleatório
     *
     * @param  int $qtd_letras      Quantidade de letras
     * @return string               Texto aleatório
     */
    private function gerarTexto(int $qtd_letras) {
        $char_set                   = $this->removeDuplicados($this->char_set);

        while(strlen($char_set) <= $qtd_letras) {
            $char_set               .= $char_set;
        }

        return substr(str_shuffle($char_set), 0, ($qtd_letras));
    }
    
    /**
     * Remove itens itens duplicados na string
     *
     * @param  string $texto        String
     * @return string
     */
    private function removeDuplicados(string $texto) {
        return implode("", array_unique(str_split($texto, 1)));
    }
    
    /**
     * Escreve o texto do captcha na imagem
     *
     * @param  GdImage $imagem      Imagem
     * @param  string $texto        Texto do captcha
     * @param  int $tamanho_fonte   Tamanho da fonte
     * @param  int $cor_fonte       Cor da fonte
     * @return GdImage $imagem      Imagem com texto
     */
    private function escreveCaptcha(GdImage $imagem, string $texto, int $tamanho_fonte, int $cor_fonte) {
        $qtd_letras                 = strlen($texto);

        for ($i = 1; $i <= $qtd_letras; $i++) { 
            $letra                  = substr($texto, $i - 1, 1);
            $angulo                 = mt_rand(-1500, 1500) / 100;

            $posicao_x              = $tamanho_fonte * $i;
            $posicao_y              = $tamanho_fonte + 10;

            imagettftext($imagem, $tamanho_fonte, $angulo, $posicao_x, $posicao_y, $cor_fonte, $this->fonte, $letra);
        }

        return $imagem;
    }
    
    /**
     * Transforma uma imagem GdImage em base64
     *
     * @param  GdImage $imagem      Imagem
     * @param  string $formato      Formato desejado
     * @return string               Base64 com a imagem pronto para impressão em tela
     */
    private function GDImageToBase64(GdImage $imagem, string $formato) {
        $formato                    = strtolower($formato);
        ob_start();

        match ($formato) {
            "gif"                   => imagegif(image: $imagem),
            "png"                   => imagepng(image: $imagem, quality: 0),
            "webp"                  => imagewebp(image: $imagem, quality: 100),
            "bitmap"                => imagebmp(image: $imagem, compressed: False),
            "jpeg"                  => imagejpeg(image: $imagem, quality: 100),
            default                 => imagejpeg(image: $imagem, quality: 100),
        };

        $base64                     = ob_get_clean();
        $base64                     = base64_encode($base64);
        return "data:image/jpeg;base64,$base64";
    }
}
