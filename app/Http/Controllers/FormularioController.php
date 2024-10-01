<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;

class FormularioController extends Controller
{
    public function index(Request $request){
        return view('formulario');
    }

    public function enviar(Request $request)
    {
        try {
            $tabId = $request->input('tab');
            $username = $request->input('username');
            $respostas = $request->input('respostas');
            $idUsuario = $this->recuperaIdUsuarioPorNome($username);

            $filePath = public_path('formulario.xlsx');
            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getSheet($tabId);
            $data = $sheet->toArray();

            $usuarioEncontrado = false;

            // Itera pelas linhas da planilha (ignorando a primeira linha de cabeçalho)
            foreach (array_slice($data, 1) as $rowIndex => $user){
                if ($user[0] === $idUsuario){
                    $usuarioEncontrado = true;

                    foreach ($respostas as $colIndex => $resposta){
                        $sheet->setCellValueByColumnAndRow($colIndex + 2, $rowIndex + 2, $resposta);
                    }

                    break;
                }
            }

            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save($filePath);

            // Retorna JSON de sucesso
            if ($usuarioEncontrado) {
                return response()->json(['success' => true, 'message' => 'Dados atualizados com sucesso.']);
            } else {
                return response()->json(['success' => false, 'message' => 'Usuário não encontrado.']);
            }
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erro ao atualizar os dados: ' . $e->getMessage()]);
        }
    }

    public function verificarUsuario(Request $request)
    {
        $username = $request->input('username');
        $password = $request->input('password');
        $idUsuario = "";
        $perfil = "";
        
        $filePath = public_path('formulario.xlsx');
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getSheet(0);
        $data = $sheet->toArray();

        foreach (array_slice($data, 1) as $user){
            if ($user[1] === $username && $user[2] === $password){
                $idUsuario = $user[0];
                $perfil = $user[3];
            }
        }

        $respostas = [];
        $perguntas = [];
        
        for ($i = 1; $i < 6; $i++) {
            $sheet = $spreadsheet->getSheet($i);
            $data = $sheet->toArray();
        
            // Captura a primeira linha sem a primeira coluna
            $primeiraLinha = array_slice($data[0], 1); 
        
            foreach (array_slice($data, 1) as $row) {
                if ($row[0] === $idUsuario) {
                    // Remove a primeira coluna dos dados da linha
                    $row = array_slice($row, 1);
                    $respostas[] = $row;
        
                    // Usa os valores da primeira linha sem a primeira coluna
                    $perguntas[] = [[$i], [$primeiraLinha, $row]];
                }
            }
        }
        
        $flattenedData = array_merge(...$respostas);
        
        return response()->json(['success' => true, 
                                 'additional_columns' => $flattenedData,
                                 'perguntas' => $perguntas,
                                 'usuario' => $username,
                                 'perfil' => $perfil]);
    }

    public function secaoSelecionada(Request $request){
        $tabId = $request->input('tab');

        // Lógica baseada na aba clicada
        switch ($tabId){
            case 'secao1-tab':
                $colunas = $this->getColunasFromExcel(1);
                return response()->json(['questions' => $colunas]);
            case 'secao2-tab':
                $colunas = $this->getColunasFromExcel(2);
                return response()->json(['questions' => $colunas]);
            case 'secao3-tab':
                $colunas = $this->getColunasFromExcel(3);
                return response()->json(['questions' => $colunas]);
            case 'secao4-tab':
                $colunas = $this->getColunasFromExcel(4);
                return response()->json(['questions' => $colunas]);
            case 'secao5-tab':
                $colunas = $this->getColunasFromExcel(5);
                return response()->json(['questions' => $colunas]);
            default:
                return response()->json(['message' => 'Aba não identificada']);
        }
    }

    public function getColunasFromExcel($sheetIndex){
        $filePath = public_path('formulario.xlsx');
        $spreadsheet = IOFactory::load($filePath);
        $spreadsheet->setActiveSheetIndex($sheetIndex);
        $sheet = $spreadsheet->getActiveSheet();
        $data = $sheet->toArray();
    
        if (empty($data)) {
            return [];
        }
    
        $firstRow = array_slice($data[0], 1);
    
        return $firstRow;
    }

    public function enviarPerguntas(Request $request)
    {
        $arrayPerguntas = $request->input('perguntas');
        $abaAtiva = $request->input('abaAtiva');

        if (is_array($arrayPerguntas) && !empty($arrayPerguntas))
        {
            $filePath = public_path('formulario.xlsx');
            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getSheet($abaAtiva);

            $lastColumn = count($arrayPerguntas) + 1;
            for ($column = 2; $column <= $lastColumn; $column++) {
                $sheet->getCellByColumnAndRow($column, 1)->setValue(null);
            }

            $column = 2;
            foreach ($arrayPerguntas as $index => $pergunta) {
                $cell = $sheet->getCellByColumnAndRow($column, 1);
                $cell->setValue($pergunta);
                $column++;
            }

            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save($filePath);

            return response()->json(['message' => $arrayPerguntas]);
        } 
        else {
            return response()->json(['message' => 'Nenhuma pergunta encontrada.'], 400);
        }
    }
    
    public function cadastrarUsuario(Request $request)
    {
        $nome = $request->input('nome');
        $senha = $request->input('senha');
        $varContUser = 0;

        if($request->input('tipo') === '1'){
            $tipo = 'admin';
        }
        else{
            $tipo = 'respondente';
        }

        $filePath = public_path('formulario.xlsx');
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getSheet(0);
        $highestRow = $sheet->getHighestRow(); 

        // Recuperar o último valor da primeira coluna
        if ($highestRow > 1){ 
            $lastValue = $sheet->getCell('A' . $highestRow)->getValue();
            $newValue = $lastValue + 1;
            $varContUser = $lastValue + 1;
        } 
        else{
            $newValue = 1; // Define o valor inicial se não houver dados
        }

        $newRow = $highestRow + 1;
        $sheet->setCellValue('A' . $newRow, $newValue);
        $sheet->setCellValue('B' . $newRow, $nome);
        $sheet->setCellValue('C' . $newRow, $senha);
        $sheet->setCellValue('D' . $newRow, $tipo);

        // Salva o arquivo atualizado
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($filePath);

        if($request->input('tipo') === '2')
        {
            for ($i = 1; $i < 6; $i++) 
            { 
                $sheet = $spreadsheet->getSheet($i);
                $highestRow = $sheet->getHighestRow(); 
                $lastValue = $sheet->getCell('A' . $highestRow)->getValue();
                $newValue = $lastValue + 1;
                $newRow = $highestRow + 1;
                $sheet->setCellValue('A' . $newRow, $varContUser);
                $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
                $writer->save($filePath);
            }
        }
        // Retorna uma resposta JSON
        return response()->json(['message' => 'Usuário cadastrado com sucesso']);
    }

    private function recuperaIdUsuarioPorNome($nome){
        $filePath = public_path('formulario.xlsx');
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getSheet(0);
        $data = $sheet->toArray();
        
        foreach (array_slice($data, 1) as $row){
            if (strtolower(trim($row[1])) === strtolower(trim($nome))){
                return $row[0];
            }
        }
    
        return null;
    }
}