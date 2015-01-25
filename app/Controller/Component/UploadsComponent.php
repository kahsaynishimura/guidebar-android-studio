<?php

App::uses('Folder', 'Utility');
App::uses('File', 'Utility');

class UploadsComponent extends Component {

//////////////////////////////////////////////////
    public function make_thumb($src, $dest, $name, $desired_width) {
        if (is_file($src)) {
            $info = pathinfo($src);

            $extension = strtolower($info['extension']);
            if (in_array($extension, array('jpg', 'jpeg', 'png', 'gif'))) {

                switch ($extension) {
                    case 'jpg':
                        $img = imagecreatefromjpeg("{$src}");
                        break;
                    case 'jpeg':
                        $img = imagecreatefromjpeg("{$src}");
                        break;
                    case 'png':
                        $img = imagecreatefrompng("{$src}");
                        break;
                    case 'gif':
                        $img = imagecreatefromgif("{$src}");
                        break;
                    default:
                        $img = imagecreatefromjpeg("{$src}");
                }
                /* read the source image */
                $width = imagesx($img);
                $height = imagesy($img);

                /* find the "desired height" of this thumbnail, relative to the desired width  */
                $desired_height = floor($height * ($desired_width / $width));

                /* create a new, "virtual" image */
                $virtual_image = imagecreatetruecolor($desired_width, $desired_height);

                /* copy source image at a resized size */
                imagecopyresized($virtual_image, $img, 0, 0, 0, 0, $desired_width, $desired_height, $width, $height);



                //verifica se existe a pasta  
                if (!is_dir($dest)) {

                    //caso não exista eu chamo o Utilitie Folder  
                    $folder = new Folder();

                    //crio a pasta já verificando se conseguiu  
                    if ($folder->create($dest)) {
                        //se conseguiu criar o diretório eu dou permissão  
                        $folder->chmod($dest, 0755, true);
                    } else {
                        //se não foi possível informo um erro  
                        throw new NotFoundException(__('Erro ao criar a pasta'));
                    }
                }



                /* create the physical thumbnail image to its destination */
                imagejpeg($virtual_image, $dest . $name);
            }
        }
    }

    public function upload(Array $arquivo, $diretorio = null, $nome = "file") {
        //Se nenhum erro foi enviado e o aruivo tem tamanho diferente de 0  
        if ((!$arquivo['error']) and ( $arquivo['size'])) {

            //removo a primeira / do titulo  
            $diretorio_local = substr($diretorio, 0, -1);

            //declaro aonde salvar os arquivos  
            $diretorio_local = WWW_ROOT . str_replace(array('/'), DS, $diretorio);
            $diretorio_local = str_replace(array(DS . DS), DS, $diretorio_local);

            //verifica se existe a pasta  
            if (!is_dir($diretorio_local)) {

                //caso não exista eu chamo o Utilitie Folder  
                $folder = new Folder();

                //crio a pasta já verificando se conseguiu  
                if ($folder->create($diretorio_local)) {
                    //se conseguiu criar o diretório eu dou permissão  
                    $folder->chmod($diretorio_local, 0755, true);
                } else {
                    //se não foi possível informo um erro  
                    throw new NotFoundException(__('Erro ao criar a pasta'));
                }
            }

            //Ok, com diretório devidamente criado, vamos declarar o arquivo temporário  
            $arquivo_tmp = new File($arquivo['tmp_name'], false);

            //pegar os dados dele  
            $dados = $arquivo_tmp->read();

            //e fecha-lo  
            $arquivo_tmp->close();

            //agora vamosc riar nosso arquivo  
            $arquivo_nome = new File($diretorio_local . DS . $nome, false, 0644);

            //cria-lo  
            $arquivo_nome->create();

            //escrever os dados armazenados  
            $arquivo_nome->write($dados);

            //e feixar o arquivo  
            $arquivo_nome->close();

            //criar o thumbnail
            $this->make_thumb(
                    WWW_ROOT . $diretorio . '/' . $nome, WWW_ROOT . $diretorio . '/thumb/', $nome, 200);
            //retorno só nome do arquivo para salvar no banco, mas poderia ser o diretório web também  
            return $diretorio . '/' . $nome;
        } else {
            //se deu erro no upload  
            throw new NotFoundException(__('Erro ao enviar arquivo'));
        }
    }

}
