<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Portuguese language strings for local_quizoverlay.
 *
 * @package    local_quizoverlay
 * @copyright  2026 Marcelo M. Almeida Júnior
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['agendamentomanual'] = 'Sobreposição de Quiz';

$string['col_attempts'] = 'Tentativas';
$string['col_course'] = 'Curso';
$string['col_password'] = 'Senha';
$string['col_quiz'] = 'Quiz';
$string['col_timeclose'] = 'Fim';
$string['col_timelimit'] = 'Limite de tempo';
$string['col_timeopen'] = 'Início';
$string['col_username'] = 'Username';

$string['csvcol_attempts'] = 'Coluna: attempts';
$string['csvcol_attempts_desc'] = 'Coluna do cabeçalho CSV que contém o número de tentativas permitidas (use 0 para ilimitado).';
$string['csvcol_data_fim'] = 'Coluna: data_fim';
$string['csvcol_data_fim_desc'] = 'Coluna do cabeçalho CSV que contém a data final (dd/mm/aaaa ou aaaa-mm-dd).';
$string['csvcol_data_in'] = 'Coluna: data_in';
$string['csvcol_data_in_desc'] = 'Coluna do cabeçalho CSV que contém a data inicial (dd/mm/aaaa ou aaaa-mm-dd).';
$string['csvcol_extras_required'] = 'Colunas extras obrigatórias';
$string['csvcol_extras_required_desc'] = 'Opcional. Lista separada por vírgula com colunas adicionais obrigatórias no cabeçalho do CSV.';
$string['csvcol_hora_fim'] = 'Coluna: hora_fim';
$string['csvcol_hora_fim_desc'] = 'Coluna do cabeçalho CSV que contém a hora final (hh:mm).';
$string['csvcol_hora_in'] = 'Coluna: hora_in';
$string['csvcol_hora_in_desc'] = 'Coluna do cabeçalho CSV que contém a hora inicial (hh:mm).';
$string['csvcol_quiz'] = 'Coluna: quiz';
$string['csvcol_quiz_desc'] = 'Coluna do cabeçalho CSV que contém o nome do Quiz (deve ser igual ao nome da atividade de Quiz no curso).';
$string['csvcol_shortname'] = 'Coluna: shortname';
$string['csvcol_shortname_desc'] = 'Coluna do cabeçalho CSV que contém o shortname do curso.';
$string['csvcol_username'] = 'Coluna: username';
$string['csvcol_username_desc'] = 'Coluna do cabeçalho CSV que contém o username do usuário.';

$string['csvfile'] = 'Arquivo CSV';
$string['csvrequiredcolumns'] = 'Colunas obrigatórias';
$string['csvsettings'] = 'Configurações de colunas do CSV';
$string['csvsettings_desc'] = 'Defina os nomes das colunas esperadas no cabeçalho do CSV. A comparação ignora maiúsculas/minúsculas e espaços.';

$string['enable_index'] = 'Habilitar página de importação CSV (index.php)';
$string['enable_index_desc'] = 'Permite acesso à página de importação de sobreposições.';
$string['enable_manage'] = 'Habilitar página administrativa (manage.php)';
$string['enable_manage_desc'] = 'Permite acesso à listagem e gerenciamento geral.';

$string['error_missingcolumns'] = 'Colunas obrigatórias ausentes: {$a}';
$string['errors'] = 'Erros';

$string['filter_perpage'] = 'Por página';
$string['filter_quiz'] = 'Quiz (nome)';
$string['filter_shortname'] = 'Curso (shortname)';
$string['filter_username'] = 'Username';

$string['generalsettings'] = 'Configurações gerais';

$string['importcsv'] = 'Importar CSV';
$string['imported'] = 'Importados';
$string['importresult'] = 'Resultado da importação';

$string['manage'] = 'Gerenciar';
$string['manageappointments'] = 'Gerenciar sobreposições';

$string['passwordpattern'] = 'Padrão de senha do aluno';
$string['passwordpattern_desc'] = 'Defina o padrão de senha a ser gerado para novos alunos. Variáveis: {dd} (dia), {mm} (mês), {yyyy} (ano), {yy} (ano 2 dígitos), {LL} (2 letras), {NN} (2 números), {L} (1 letra), {N} (1 número).';
$string['passwordsettings'] = 'Configurações de senha';

$string['pluginname'] = 'Sobreposição de Quiz';

$string['privacy:metadata:local_quizoverlay'] = 'Registros de sobreposição de quiz armazenados.';
$string['privacy:metadata:local_quizoverlay:attempts'] = 'Tentativas permitidas.';
$string['privacy:metadata:local_quizoverlay:courseid'] = 'ID do curso.';
$string['privacy:metadata:local_quizoverlay:quiz'] = 'Nome do quiz.';
$string['privacy:metadata:local_quizoverlay:timeclose'] = 'Data/hora final da sobreposição.';
$string['privacy:metadata:local_quizoverlay:timecreated'] = 'Data/hora de criação.';
$string['privacy:metadata:local_quizoverlay:timemodified'] = 'Data/hora de atualização.';
$string['privacy:metadata:local_quizoverlay:timeopen'] = 'Data/hora inicial da sobreposição.';
$string['privacy:metadata:local_quizoverlay:userid'] = 'ID do usuário.';

$string['privacy:metadata:local_quizoverlay_upass'] = 'Senha armazenada para o usuário.';
$string['privacy:metadata:local_quizoverlay_upass:password'] = 'Senha gerada.';
$string['privacy:metadata:local_quizoverlay_upass:timecreated'] = 'Data/hora de criação.';
$string['privacy:metadata:local_quizoverlay_upass:userid'] = 'ID do usuário.';

$string['resetfilters'] = 'Limpar filtros';
$string['search'] = 'Buscar';
$string['skipped'] = 'Ignorados';

$string['timelimitcol_quiz'] = 'Coluna: timelimit';
$string['timelimitcol_quiz_desc'] = 'Coluna do cabeçalho CSV que contém o Limite de tempo (em horas) para aplicar como timelimit (em segundos) na sobreposição do quiz. Ex.: 1 = 3600.';

$string['totalresults'] = 'Total';
