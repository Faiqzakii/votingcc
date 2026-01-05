<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\User::query();

        // Filter: Has Not Voted
        if ($request->has('not_voted')) {
            $query->whereDoesntHave('votes');
        }

        $users = $query->where('email', '!=', 'admin@example.com') // Exclude admin from list? strictly speaking admin is a user too but usually we hide superadmin
                       ->orderBy('name')
                       ->paginate(10);

        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'username' => 'required|unique:users',
            'password' => 'required|min:6',
        ]);

        \App\Models\User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->username . '@example.com', // Dummy email or optional
            'password' => bcrypt($request->password),
        ]);

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil dibuat.');
    }

    public function edit(\App\Models\User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, \App\Models\User $user)
    {
        $request->validate([
            'name' => 'required',
            'username' => 'required|unique:users,username,' . $user->id,
        ]);

        $data = [
            'name' => $request->name,
            'username' => $request->username,
        ];

        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroy(\App\Models\User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'Pengguna berhasil dihapus.');
    }

    public function import()
    {
        return view('users.import');
    }

    public function downloadTemplate()
    {
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=users_template.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = ['name', 'username', 'password'];

        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            
            // Example data
            fputcsv($file, ['John Doe', 'john.doe', 'secret123']);
            fputcsv($file, ['Jane Smith', 'jane.smith', '']); // Empty password uses default
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function storeImport(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt|max:2048', // 2MB max
        ]);

        $file = $request->file('file');
        
        // Simple CSV parsing
        $handle = fopen($file->getPathname(), "r");
        $header = fgetcsv($handle, 1000, ","); // Skip header
        
        $importedCount = 0;
        $skippedCount = 0;

        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            // Mapping based on template: 0=name, 1=username, 2=password
            $name = $data[0] ?? null;
            $username = $data[1] ?? null;
            $password = $data[2] ?? null;

            if (!$name || !$username) {
                continue; // Skip invalid rows
            }

            // Check duplicate
            if (\App\Models\User::where('username', $username)->exists()) {
                $skippedCount++;
                continue;
            }

            \App\Models\User::create([
                'name' => $name,
                'username' => $username,
                'password' => bcrypt($password ?: 'password'), // Default password
            ]);
            
            $importedCount++;
        }
        
        fclose($handle);

        $message = "Import selesai. $importedCount pengguna ditambahkan.";
        if ($skippedCount > 0) {
            $message .= " $skippedCount dilewati (duplikat).";
        }

        return redirect()->route('users.index')->with('success', $message);
    }
}
