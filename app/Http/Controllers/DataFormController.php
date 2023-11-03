<?php

namespace App\Http\Controllers;

use App\Application;
use Exception;
use Google\Service\Sheets;
use Google_Client;
use Google_Service_Sheets;
use Google_Service_Sheets_Spreadsheet;
use Google_Service_Sheets_ValueRange;
use Illuminate\Http\Request;
use Google\Client as GoogleClient;
use Google\Service\Sheets as GoogleSheets;
use Illuminate\Support\Facades\Validator;

class DataFormController extends Controller
{
    public function dataForm(Request $request)
    {
        // Здесь вы можете возвращать представление или данные, необходимые для DataForm компонента Vue.js
        return view('index');
    }
    public function saveDataAndCreateGoogleSheet(Request $request)
    {
        try {
            // Сохраните данные из формы в базу данных
            $data = $request->all();
            $rules = [
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'phone_number' => [
                    'required',
                    'string',
                    'max:255',
                    'regex:/^\+[0-9]{1,4}-[0-9]{1,14}$/',
                ],
                'application_text' => 'required|string',
            ];

            $messages = [
                'required' => 'Поле :attribute обязательно для заполнения.',
                'string' => 'Поле :attribute должно быть строкой.',
                'max' => 'Поле :attribute не должно превышать :max символов.',
            ];

            $validator = Validator::make($data, $rules, $messages);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 400);
            }
            $application = Application::create($data);

            // Создайте клиент Google API
            $client = $this->getClient();

            // Создайте экземпляр Google Sheets API
            $service = new Google_Service_Sheets($client);

            // Создайте новую таблицу
            $spreadsheet = new Google_Service_Sheets_Spreadsheet([
                'properties' => [
                    'title' => 'Заявки от клиентов'
                ]
            ]);

            $spreadsheet = $service->spreadsheets->create($spreadsheet);
            $spreadsheetId = $spreadsheet->spreadsheetId;

            // Получите имя листа, в который хотите вставить данные
            $worksheetName = 'Лист1';

            // Вставьте данные в лист
            $values = [
                ["Igor", "2425-245-224545", "Orlov", "Berlin", "35", date('Y-m-d H:i:s')]
            ];

            $range = $worksheetName;
            $body = new Google_Service_Sheets_ValueRange([
                'values' => $values
            ]);

            $params = [
                'valueInputOption' => 'RAW'
            ];

            $insert = [
                'insertDataOption' => 'INSERT_ROWS'
            ];

            $result = $service->spreadsheets_values->append($spreadsheetId, $range, $body, $params, $insert);

            // Сохраните ссылку на Google-таблицу в базу данных
            Application::where('id', $application->id)->update(['google_sheet_url' => $spreadsheet->spreadsheetUrl]);

            return response()->json(['message' => 'Данные успешно сохранены и Google-таблица создана.']);
        } catch (Exception $exception) {
            dd($exception);
        }
    }
    /**
     * @throws \Google\Exception
     */
    private function getClient()
    {
        $client = new Google_Client();
        $client->setApplicationName('My Laravel App');
        $client->setRedirectUri('http://localhost:8000/api'); // Замените на ваш URI перенаправления
        $client->setScopes(Google_Service_Sheets::SPREADSHEETS);
        $client->setAuthConfig(storage_path('client_credentials.json')); // Укажите путь к вашему JSON-ключу
        return $client;
    }


    public function createSpreadsheet()
    {

        // Создаем экземпляр Google Sheets API
        $service = new Google_Service_Sheets($client);
        // Создаем новую таблицу
        $spreadsheet = new Google_Service_Sheets_Spreadsheet([
            'properties' => [
                'title' => 'Заявки'
            ]
        ]);
        $spreadsheet = $service->spreadsheets->create($spreadsheet);
        $spreadsheetId = $spreadsheet->spreadsheetId;

        return "Таблица создана с идентификатором: $spreadsheetId";
    }

    public function updateSpreadsheet()
    {
        $client = $this->getClient();

        // Идентификатор вашей таблицы
        $spreadsheetId = 'Ваш_идентификатор_таблицы';

        // Данные для внесения
        $values = [
            ["Значание1", "Значение2"],
            ["Значение3", "Значение4"],
        ];

        $range = 'A1:B2';

        $valueRange = new Google_Service_Sheets_ValueRange();
        $valueRange->setValues(['values' => $values]);

        $service = new Google_Service_Sheets($client);
        $result = $service->spreadsheets_values->update($spreadsheetId, $range, $valueRange, [
            'valueInputOption' => 'RAW'
        ]);

        return "Данные успешно внесены в таблицу!";
    }

}
