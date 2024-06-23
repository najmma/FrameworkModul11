<?php

namespace App\Http\Controllers;

use App\Http\Resources\APIResource;
use PDF;
use App\Models\Employee;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use RealRashid\SweetAlert\Facades\Alert;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EmployeesExport;
use GuzzleHttp\Client;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pageTitle = 'Employee List';

        confirmDelete();

        $employeeData = $this->handleApiRequest('get', 'employees');
        return view('employee.index', ['employees' => $employeeData, 'pageTitle' => $pageTitle]);
    }




    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pageTitle = 'Create Employee';
        // ELOQUENT
        $positions = Position::all();
        return view('employee.create', compact('pageTitle', 'positions'));
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Melakukan Pesan Validasi Nilai Input
        $messages = [
            'required' => ':Attribute must be filled.',
            'email' => 'Fill :attribute with the correct format.',
            'numeric' => 'Fill :attribute with numeric.',
            'email.unique' => 'The email address has been registered.',
        ];

        // Validasi
        $validator = Validator::make($request->all(), [
            'firstName' => 'required',
            'lastName' => 'required',
            'email' => 'required|email|unique:employees,email',
            'age' => 'required|numeric',
        ], $messages);


        // Check Response Validasi
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $employeeData = [
            'firstName' => $request->input('firstName'),
            'lastName' => $request->input('lastName'),
            'email' => $request->input('email'),
            'age' => $request->input('age'),
            'position_id' => $request->input('position'),
            'cv' => $request->file('cv')
        ];

        // Send API request
        $client = $this->getClient();
        $response = $client->post('employees', [
            'json' => $employeeData
        ]);

        $responseData = json_decode($response->getBody(), true);
        Alert::success('Updated Successfully', 'Employee Data Updated Successfully.');
        return redirect()->route('employees.index')->with('success', 'Employee created successfully');
    }




    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $pageTitle = 'Employee Detail';
        $employeeData = $this->handleApiRequest('get', 'employees/' . $id);
        return view('employee.show', ['employee' => $employeeData, 'pageTitle' => $pageTitle]);
    }



    public function getData(Request $request)
{
    $employees = Employee::with('position');

    if ($request->ajax()) {
        return datatables()->of($employees)
            ->addIndexColumn()
            ->addColumn('actions', function($employee) {
                return view('employee.actions', compact('employee'));
            })
            ->toJson();
    }
}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $employee = Employee::find($id);
    $positions = Position::all();  // Ambil semua posisi dari database
    return view('employee.edit', compact('employee', 'positions'));
    }



    public function update(Request $request, string $id)
    {
        // Melakukan Pesan Validasi Nilai Input
        $messages = [
            'required' => ':Attribute must be filled.',
            'email' => 'Fill :attribute with the correct format.',
            'numeric' => 'Fill :attribute with numeric.',
            'email.unique' => 'The email address has been registered.',
        ];

        // Validasi
        $validator = Validator::make($request->all(), [
            'firstName' => 'required',
            'lastName' => 'required',
            'email' => 'required|email|unique:employees,email,' . $id,
            'age' => 'required|numeric',
        ], $messages);

        // Check Response Validasi
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Prepare data for API request
        $employeeData = [
            'firstName' => $request->input('firstName'),
            'lastName' => $request->input('lastName'),
            'email' => $request->input('email'),
            'age' => $request->input('age'),
            'position_id' => $request->input('position'),
            'cv' => $request->file('cv')
        ];

        // Send API request
            $client = $this->getClient();
            $response = $client->put('employees/' . $id, [
                'json' => $employeeData
            ]);

            $responseData = json_decode($response->getBody(), true);
            Alert::success('Updated Successfully', 'Employee Data Updated Successfully.');
            return redirect()->route('employees.index');
    }




    public function destroy(string $id)
    {

        $this->handleApiRequest('delete', 'employees/' . $id);
        Alert::success('Deleted Successfully', 'Employee Data Deleted Successfully.');
        return redirect()->route('employees.index');

    }



    public function exportExcel()
    {
        return Excel::download(new EmployeesExport, 'employees.xlsx');
    }

    public function exportPdf()
    {


    $employees = Employee::all();

    $pdf = PDF::loadView('employee.export_pdf', compact('employees'));

    return $pdf->download('employees.pdf');
    }

    private function getClient()
    {
        return new Client([
            'base_uri' => "http://127.0.0.1:8000/api/",
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . session('api_token'),
            ],
        ]);
    }

    private function handleApiRequest($method, $uri, $params = [])
    {
        try {
            $response = $this->getClient()->$method($uri, $params);
            $responseBody = json_decode($response->getBody(), true);

            if ($response->getStatusCode() != 200) {
                throw new \Exception('Failed to fetch data.');
            }

            return $responseBody['data'];
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to fetch data: ' . $e->getMessage()]);
        }
    }





}


