<?php

use App\Models\AmiCycle;
use App\Models\AmiSubmission;
use App\Models\Prodi;
use App\Models\Standard;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Role::findOrCreate('auditee');
    Role::findOrCreate('admin');
    Role::findOrCreate('ST1');
    Role::findOrCreate('ST2');
});

function makeAmiContext(): array
{
    $standard = Standard::create(['code' => 'ST1', 'name' => 'Standar 1']);
    $otherStandard = Standard::create(['code' => 'ST2', 'name' => 'Standar 2']);
    $prodi = Prodi::create(['code' => 'PRODI-A', 'name' => 'Prodi A', 'jenjang' => 'S1']);
    $cycle = AmiCycle::create(['title' => 'AMI 2026', 'status' => 'active']);
    $owner = User::factory()->create(['is_active' => true]);
    $peer = User::factory()->create(['is_active' => true]);
    $outsider = User::factory()->create(['is_active' => true]);
    $owner->syncRoles(['auditee', 'ST1']);
    $peer->syncRoles(['auditee', 'ST1']);
    $outsider->syncRoles(['auditee', 'ST2']);
    $submission = AmiSubmission::create([
        'cycle_id' => $cycle->id,
        'prodi_id' => $prodi->id,
        'standard_id' => $standard->id,
        'owner_id' => $owner->id,
        'status' => 'draft',
    ]);

    return compact('standard', 'otherStandard', 'prodi', 'cycle', 'owner', 'peer', 'outsider', 'submission');
}

test('auditee assigned to the same standard can view another auditee submission', function () {
    $ctx = makeAmiContext();

    $this->actingAs($ctx['peer'])
        ->get(route('internal.ami.show', $ctx['submission']))
        ->assertOk();
});

test('auditee without the same standard cannot view submission', function () {
    $ctx = makeAmiContext();

    $this->actingAs($ctx['outsider'])
        ->get(route('internal.ami.show', $ctx['submission']))
        ->assertForbidden();
});

test('auditee assigned to the same standard cannot edit another auditee submission', function () {
    $ctx = makeAmiContext();

    $this->actingAs($ctx['peer'])
        ->post(route('internal.ami.references.store', $ctx['submission']), [
            'ami_question_id' => 999,
            'title' => 'Tidak boleh tersimpan',
            'url' => 'https://example.test/document',
        ])->assertForbidden();
});

test('auditee can open AMI index for all prodis when no submission exists', function () {
    $ctx = makeAmiContext();
    $ctx['submission']->delete();

    $this->actingAs($ctx['peer'])
        ->get(route('internal.ami.index'))
        ->assertOk()
        ->assertSee('Belum ada submission untuk standar yang ditugaskan kepada Anda.');
});
