<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Credential;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $groups = Group::where('created_by', auth()->id())
            ->withCount('credentials')
            ->orderBy('name')
            ->get();

        $navigationTree = $this->getNavigationTree();

        return Inertia::render('Groups/Index', [
            'groups' => $groups,
            'navigationTree' => $navigationTree
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $navigationTree = $this->getNavigationTree();
        
        return Inertia::render('Groups/Create', [
            'navigationTree' => $navigationTree
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7',
            'parent_id' => 'nullable|exists:groups,id',
        ]);

        Group::create([
            'name' => $request->name,
            'description' => $request->description,
            'parent_id' => $request->parent_id,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('groups.index')
            ->with('success', 'Group created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Group $group)
    {
        if ($group->created_by !== auth()->id()) {
            abort(403);
        }
        
        $credentials = Credential::where('group_id', $group->id)
            ->where('created_by', auth()->id())
            ->orderBy('title')
            ->get();

        return Inertia::render('Groups/Show', [
            'group' => $group,
            'credentials' => $credentials
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Group $group)
    {
        if ($group->created_by !== auth()->id()) {
            abort(403);
        }
        
        $credentials = Credential::where('group_id', $group->id)
            ->where('created_by', auth()->id())
            ->orderBy('title')
            ->get();
            
        $availableGroups = Group::where('created_by', auth()->id())
            ->where('id', '!=', $group->id)
            ->orderBy('name')
            ->get();
            
        $navigationTree = $this->getNavigationTree();
        
        return Inertia::render('Groups/Edit', [
            'group' => $group,
            'credentials' => $credentials,
            'availableGroups' => $availableGroups,
            'navigationTree' => $navigationTree
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Group $group)
    {
        if ($group->created_by !== auth()->id()) {
            abort(403);
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7',
        ]);

        $group->update([
            'name' => $request->name,
            'description' => $request->description,
            'color' => $request->color ?? $group->color,
        ]);

        return redirect()->route('groups.index')
            ->with('success', 'Group updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Group $group)
    {
        if ($group->created_by !== auth()->id()) {
            abort(403);
        }
        
        // Move credentials to ungrouped (null group_id)
        Credential::where('group_id', $group->id)
            ->where('created_by', auth()->id())
            ->update(['group_id' => null]);
        
        $group->delete();

        return redirect()->route('groups.index')
            ->with('success', 'Group deleted successfully.');
    }

    /**
     * Move credentials between groups
     */
    public function move(Request $request, Group $group)
    {
        if ($group->created_by !== auth()->id()) {
            abort(403);
        }
        
        $request->validate([
            'credential_ids' => 'required|array',
            'credential_ids.*' => 'exists:credentials,id',
            'target_group_id' => 'nullable|exists:groups,id',
        ]);

        Credential::whereIn('id', $request->credential_ids)
            ->where('created_by', auth()->id())
            ->update(['group_id' => $request->target_group_id]);

        return response()->json(['message' => 'Credentials moved successfully.']);
    }

    /**
     * Export group credentials
     */
    public function export(Group $group)
    {
        if ($group->created_by !== auth()->id()) {
            abort(403);
        }
        
        $credentials = Credential::where('group_id', $group->id)
            ->where('created_by', auth()->id())
            ->get();

        $csvData = "Title,Username,Password,URL,Notes\n";
        
        foreach ($credentials as $credential) {
            $csvData .= sprintf(
                '"%s","%s","%s","%s","%s"' . "\n",
                $credential->title,
                $credential->username,
                decrypt($credential->password),
                $credential->url ?? '',
                $credential->notes ?? ''
            );
        }

        return response($csvData)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $group->name . '_credentials.csv"');
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
