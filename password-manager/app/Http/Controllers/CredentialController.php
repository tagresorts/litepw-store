<?php

namespace App\Http\Controllers;

use App\Models\Credential;
use App\Models\Group;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CredentialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $credentials = Credential::with('group')
            ->where('created_by', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        $navigationTree = $this->getNavigationTree();

        return Inertia::render('Credentials/Index', [
            'credentials' => $credentials,
            'navigationTree' => $navigationTree
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $groups = Group::where('created_by', auth()->id())->get();
        $navigationTree = $this->getNavigationTree();
        
        return Inertia::render('Credentials/Create', [
            'groups' => $groups,
            'navigationTree' => $navigationTree
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'credential_entries' => 'required|array|min:1',
            'credential_entries.*.username' => 'required|string|max:255',
            'credential_entries.*.password' => 'required|string',
            'credential_entries.*.urls' => 'nullable|array',
            'credential_entries.*.urls.*' => 'nullable|string',
            'notes' => 'nullable|string',
            'group_id' => 'nullable|exists:groups,id',
        ]);

        // Create the main credential record
        $credential = Credential::create([
            'title' => $request->title,
            'username' => $request->credential_entries[0]['username'], // Primary username
            'encrypted_password' => encrypt($request->credential_entries[0]['password']), // Primary password
            'url' => !empty($request->credential_entries[0]['urls']) ? $request->credential_entries[0]['urls'][0] : null, // Primary URL
            'notes' => $request->notes,
            'group_id' => $request->group_id,
            'created_by' => auth()->id(),
        ]);

        // Store additional credential entries in custom_fields as JSON
        $additionalEntries = [];
        for ($i = 1; $i < count($request->credential_entries); $i++) {
            $entry = $request->credential_entries[$i];
            $additionalEntries[] = [
                'username' => $entry['username'],
                'password' => encrypt($entry['password']), // Encrypt additional passwords too
                'urls' => array_filter($entry['urls'] ?? []), // Remove empty URLs
            ];
        }

        // Store additional entries in custom_fields
        if (!empty($additionalEntries)) {
            $credential->update([
                'custom_fields' => [
                    'additional_entries' => $additionalEntries
                ]
            ]);
        }

        return redirect()->route('credentials.index')
            ->with('success', 'Credential created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Credential $credential)
    {
        if ($credential->created_by !== auth()->id()) {
            abort(403);
        }
        
        return Inertia::render('Credentials/Show', [
            'credential' => $credential->load('group')
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Credential $credential)
    {
        if ($credential->created_by !== auth()->id()) {
            abort(403);
        }
        
        $groups = Group::where('created_by', auth()->id())->get();
        
        return Inertia::render('Credentials/Edit', [
            'credential' => $credential,
            'groups' => $groups
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Credential $credential)
    {
        if ($credential->created_by !== auth()->id()) {
            abort(403);
        }
        
        $request->validate([
            'title' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'password' => 'required|string',
            'url' => 'nullable|url',
            'notes' => 'nullable|string',
            'group_id' => 'nullable|exists:groups,id',
        ]);

        $credential->update([
            'title' => $request->title,
            'username' => $request->username,
            'encrypted_password' => encrypt($request->password),
            'url' => $request->url,
            'notes' => $request->notes,
            'group_id' => $request->group_id,
        ]);

        return redirect()->route('credentials.index')
            ->with('success', 'Credential updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Credential $credential)
    {
        if ($credential->created_by !== auth()->id()) {
            abort(403);
        }
        
        $credential->delete();

        return redirect()->route('credentials.index')
            ->with('success', 'Credential deleted successfully.');
    }

    /**
     * Toggle favorite status
     */
    public function toggleFavorite(Credential $credential)
    {
        if ($credential->created_by !== auth()->id()) {
            abort(403);
        }
        
        $credential->update([
            'is_favorite' => !$credential->is_favorite
        ]);

        return response()->json(['is_favorite' => $credential->is_favorite]);
    }

    /**
     * Generate a secure password
     */
    public function generatePassword(Request $request)
    {
        $length = $request->get('length', 16);
        $includeSymbols = $request->get('symbols', true);
        
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        if ($includeSymbols) {
            $chars .= '!@#$%^&*()_+-=[]{}|;:,.<>?';
        }
        
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[random_int(0, strlen($chars) - 1)];
        }
        
        return response()->json(['password' => $password]);
    }

    /**
     * Get decrypted password
     */
    public function getPassword(Credential $credential)
    {
        if ($credential->created_by !== auth()->id()) {
            abort(403);
        }
        
        return response()->json(['password' => decrypt($credential->password)]);
    }

    /**
     * Search credentials
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        
        $credentials = Credential::with('group')
            ->where('created_by', auth()->id())
            ->where(function($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('username', 'like', "%{$query}%")
                  ->orWhere('url', 'like', "%{$query}%")
                  ->orWhere('notes', 'like', "%{$query}%");
            })
            ->get();

        return Inertia::render('Credentials/Search', [
            'credentials' => $credentials,
            'query' => $query
        ]);
    }

    /**
     * Get navigation tree for sidebar
     */
    private function getNavigationTree()
    {
        $groups = Group::where('created_by', auth()->id())
            ->orderBy('name')
            ->get();

        return $groups->map(function ($group) {
            return [
                'id' => $group->id,
                'name' => $group->name,
                'description' => $group->description,
                'level' => 0,
                'credential_count' => $group->credentials()->count(),
                'children' => [],
            ];
        })->toArray();
    }
}
