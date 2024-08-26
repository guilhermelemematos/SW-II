<?php
    header('Content-Type:application/json');
    include 'conexao.php';

    $metodo = $_SERVER['REQUEST_METHOD'];
    $url = $_SERVER['REQUEST_URI'];

    $path = parse_url($url, PHP_URL_PATH);
    $path = trim($path,'/');
    $pathparts = explode('/',$path);

    //CRIANDO AS VARIAVEIS PARA CADA PARTE DA URL

    $primeira = isset($pathparts[0]) ? $pathparts[0] : ''; 
    $segunda = isset($pathparts[1]) ? $pathparts[1] : '';
    $terceira = isset($pathparts[2]) ? $pathparts[2] : '';
    $quarta = isset($pathparts[3]) ? $pathparts[3] : '';

    //MONTANDO A RESPOSTA DA API EM JSON

    $response = [
        'metodo' => $metodo,
        'primeiraParte' => $primeira,
        'segundaParte' => $segunda,
        'terceiraParte' => $terceira,
        'quartaParte' => $quarta
    ];

    //SELEÇÃO DO MÉTODO

    switch($metodo){
        case 'GET':
            // lógica para GET
            if($terceira == 'alunos' && $quarta ==''){
                lista_alunos();
            }
            elseif($terceira == 'alunos' && $quarta !=''){
                lista_um_aluno($quarta);
            }
            elseif($terceira == 'cursos' && $quarta == ''){
                lista_cursos();
                // echo json_encode(
                //     [
                //         'mensagem' => 'LISTA TODOS OS CURSOS!'
                //     ]
                // );
            }
            elseif($terceira == 'cursos' && $quarta !=''){
                lista_um_curso($quarta);
                // echo json_encode(
                //     [
                //         'mensagem' => 'LISTA DE UM CURSO!',
                //         'id_curso' => $quarta
                //     ]
                // );
            }
            
            break;
        case 'POST':
            //lógica para POST
            break;
        case 'PUT':
            //lógica para PUT
            break;
        case 'DELETE':
            //lógica para o DELETE
            break;
        default:
            echo json_encode(
                [
                    'mensagem' => 'Método não permitido!'
                ]
            );
            break;
    }



    function lista_alunos(){
        global $conexao;
        $resultado = $conexao->query("SELECT * FROM alunos");
        $alunos = $resultado->fetch_all(MYSQLI_ASSOC);
        echo json_encode(
            [
                'mensagem' => 'LISTA TODOS OS ALUNOS!',
                'dados' => $alunos
            ]
        );
    }

    function lista_um_aluno($quarta){
        global $conexao;
        $stmt = $conexao->prepare("SELECT * FROM alunos WHERE id = ?");
        $stmt->bind_param('i',$quarta);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $aluno = $resultado->fetch_assoc();

        if($aluno == ''){
            echo json_encode(
                [
                    'mensagem' => 'NÃO FOI ENCONTRADO O ALUNO ACIMA!'
                ]
            );
        }else{
            echo json_encode(
                [
                    'mensagem' => 'LISTA DE UM ALUNO!',
                    'dados_aluno' => $aluno
                ]
            );
        }

        
    }

    function lista_cursos(){
        global $conexao;
        $resultado = $conexao->query("SELECT * FROM cursos");
        $cursos = $resultado->fetch_all(MYSQLI_ASSOC);
        echo json_encode(
            [
                'mensagem' => 'LISTA TODOS OS CURSOS!',
                'dados' => $cursos
            ]
        );
    }

    function lista_um_curso($quarta){
        global $conexao;
        $stmt = $conexao->prepare("SELECT * FROM cursos WHERE id_curso = ?");
        $stmt->bind_param('i',$quarta);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $curso = $resultado->fetch_assoc();

        if($curso == ''){
            echo json_encode(
                [
                    'mensagem' => 'NÃO FOI ENCONTRADO O CURSO ACIMA!'
                ]
            );
        }else{
            echo json_encode(
                [
                    'mensagem' => 'LISTA DE UM CURSO!',
                    'dados_curso' => $curso
                ]
            );
        }

        
    }

    function insere_curso(){
        global $conexao;
        //OPCAO 1 COM JSON
        // $input = json_decode(file_get_contents('php://input'), true);
        // $nome_curso = $input{'nome_curso'}

        //OPCAO 2 COM PARAMETROS
        $nome_curso = $_GET['nome_curso'];

        $sql = "INSERT INTO cursos (nome_curso) VALUES ('$nome_curso')";

        if($conexao->query($sql) == TRUE){
            echo json_encode([
                'mensagem' => 'CURSO CADASTRADO COM SUCESSO'
            ])
        }
    }

    function insere_aluno(){
        global $conexao;
        //Para inserir um aluno é obrigatório que haja um curso desejado já cadastrado!
        //NEste exemplo vamos passar os parâmetros via JSON
        $input = json_decode(file_get_contents('php://input'), true);
        $id_curso = $input['fk_cursos_id_curso'];
        $nome = $input['nome'];
        $email = $input['email'];

        $sql = "INSERT INTO alunos (nome,email,fk_cursos_id_cruso) VALUES ('$nome','$email','$id_curso')";

        if($conexao->query($sql) == TRUE){
            echo json_encode([
                'mensagem' => 'ALUNO CADASTRADO COM SUCESSO'
            ]);
        }
        else {
            echo json_encode([
                'mensagem' => 'ERRO NO CADASTRO DO ALUNO'
            ]);
        }
    }
    function atualiza_aluno(){
        global $conexao;
        //Para atualizar um aluno é obrigatório o envio do ID do aluno
        //Precisa enviar todos os dados que serem atualizados (nome, email, curso, etc)
        //Aqui pode ser pensada vários tipos de lógica, como por exemplo se somente um destes campos vierem preenchidos. Para este
        //exemplo vamos pensar em todos os campos serão enviados preenchidos, com ou sem alterações

        $input = json_decode(file_get_contentes('php://input'), true);
        $id_novo = $input['id'];
        $nome_novo = $input['nome_novo'];
        $email_novo = $input['email_novo'];

        $sql = "UPDATE alunos SET nome = '$nome_novo', email = '$email_novo' WHERE id ='id'"

        if($conexao->query($sql) == TRUE){
                echo json_encode([
                    'mensagem' => 'ALUNO ATUALIZADO COM SUCESSO'
                ]);
            }
            else {
                echo json_encode([
                    'mensagem' => 'ERRO ATUALIZAÇÂO DO ALUNO'
                ])
            }
    };

    function atualiza_curso({
        global $conexao
        //Para atualizar um aluno é obrigatório o envio do ID do aluno
        //Precisa enviar todos os dados que serem atualizados (nome, email, curso, etc)
        //Aqui pode ser pensada vários tipos de lógica, como por exemplo se somente um destes campos vierem preenchidos. Para este
        //exemplo vamos pensar em todos os campos serão enviados preenchidos, com ou sem alterações

        $input = json_decode(file_get_contentes('php://input'), true);
        $id_curso = $input['id_curso'];
        $nome_curso_novo = $input['nome_curso_novo'];

        $sql = "UPDATE cursos SET nome_curso = '$nome_curso_novo' WHERE id_curso ='id_curso'"

        if($conexao->query($sql) == TRUE){
                echo json_encode([
                    'mensagem' => 'CURSO ATUALIZADO COM SUCESSO'
                ]);
            }
            else {
                echo json_encode([
                    'mensagem' => 'ERRO ATUALIZAÇÂO DO CURSO'
                ])
            }
    })

    function remove_aluno(){
        //Lógica idem atualizada, mas aqui precisamos somente do id
        global $conexao;
        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id'];
        $sql = "DELETE FROM alunos WHERE 'id =$id'";
        if($conexao->query($sql) == TRUE)[
            echo json_encode([
                'mensagem' => 'ALUNO REMOVIDO COM SUCESSO'
            ]);
        ]
        else{
            echo json_encode([
                'mensagem' => 'ERRO NA REMOÇÂO DO ALUNO'
            ])
        }

    }

    function remove_curso({
        global $conexao;
        $input = json_decode(file_get_contents('php://input'), true);
        $id_curso = $inpút['id_curso'];
        $sql = "DELETE FROM cursos WHERE id_curso = '$id_curso'";
        if($conexao->query($sql) == TRUE){
            echo json_encode([
                'mensagem' => 'CURSO REMOVIDO COM SUCESSO'
            ]);
        }
        else {
            echo json_encode([
                'mensagem' => 'ERRO NA REMOÇÂO DO CURSO'
            ]);
        }
    })
?>