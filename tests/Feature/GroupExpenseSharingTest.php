<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\GroupMember;
use App\Models\GroupExpense;
use App\Models\GroupExpenseShare;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class GroupExpenseSharingTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_group()
    {
        $user = User::factory()->create([
            'is_admin' => false,
            'menu_split_groups' => true,
        ]);

        $this->actingAs($user);

        Livewire::test(\App\Livewire\User\Groups\GroupList::class)
            ->set('name', 'Roommates 2026')
            ->call('createGroup')
            ->assertHasNoErrors()
            ->assertStatus(200);

        $this->assertDatabaseHas('groups', [
            'name' => 'Roommates 2026',
            'created_by' => $user->id,
        ]);

        $group = Group::where('name', 'Roommates 2026')->first();

        // Creator should automatically be a member
        $this->assertDatabaseHas('group_members', [
            'group_id' => $group->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_user_can_join_group_via_code()
    {
        $creator = User::factory()->create([
            'is_admin' => false,
            'menu_split_groups' => true,
        ]);
        
        $group = Group::create([
            'name' => 'Shared Apt',
            'created_by' => $creator->id,
        ]);
        
        GroupMember::create([
            'group_id' => $group->id,
            'user_id' => $creator->id,
        ]);

        $joiner = User::factory()->create([
            'is_admin' => false,
            'menu_split_groups' => true,
        ]);

        $this->actingAs($joiner);

        Livewire::test(\App\Livewire\User\Groups\GroupList::class)
            ->set('joinCode', $group->invite_token)
            ->call('joinGroup')
            ->assertHasNoErrors()
            ->assertStatus(200);

        $this->assertDatabaseHas('group_members', [
            'group_id' => $group->id,
            'user_id' => $joiner->id,
        ]);
    }

    public function test_join_via_controller_route()
    {
        $creator = User::factory()->create([
            'is_admin' => false,
        ]);
        $group = Group::create([
            'name' => 'Trip',
            'created_by' => $creator->id,
        ]);
        GroupMember::create([
            'group_id' => $group->id,
            'user_id' => $creator->id,
        ]);

        $user = User::factory()->create([
            'is_admin' => false,
        ]);

        $response = $this->actingAs($user)->get(route('user.groups.join', $group->invite_token));

        $response->assertRedirect(route('user.group-detail', $group->id));
        $this->assertDatabaseHas('group_members', [
            'group_id' => $group->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_add_member_by_unique_code_sends_notification()
    {
        $creator = User::factory()->create(['is_admin' => false]);
        $group = Group::create([
            'name' => 'Apt',
            'created_by' => $creator->id,
        ]);
        GroupMember::create(['group_id' => $group->id, 'user_id' => $creator->id]);

        $other = User::factory()->create([
            'is_admin' => false,
            'unique_code' => 'TESTCODE',
        ]);

        $this->actingAs($creator);

        Livewire::test(\App\Livewire\User\Groups\GroupDetail::class, ['id' => $group->id])
            ->set('newMemberInput', 'TESTCODE')
            ->call('addMember')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('group_members', [
            'group_id' => $group->id,
            'user_id' => $other->id,
        ]);

        // Assert notification sent
        $this->assertCount(1, $other->unreadNotifications);
        $this->assertEquals("{$creator->name} added you to the group \"Apt\".", $other->unreadNotifications->first()->data['message']);
    }

    public function test_equal_split_calculation()
    {
        $creator = User::factory()->create(['is_admin' => false]);
        $group = Group::create(['name' => 'SplitTest', 'created_by' => $creator->id]);
        
        $user1 = User::factory()->create(['is_admin' => false]);
        $user2 = User::factory()->create(['is_admin' => false]);
        
        GroupMember::create(['group_id' => $group->id, 'user_id' => $creator->id]);
        GroupMember::create(['group_id' => $group->id, 'user_id' => $user1->id]);
        GroupMember::create(['group_id' => $group->id, 'user_id' => $user2->id]);

        $this->actingAs($creator);

        Livewire::test(\App\Livewire\User\Groups\GroupDetail::class, ['id' => $group->id])
            ->set('description', 'Dinner')
            ->set('amount', '90.00')
            ->set('splitMode', 'equal')
            ->set('paidBy', $creator->id)
            ->set('selectedMembers', [$creator->id, $user1->id, $user2->id])
            ->call('saveExpense')
            ->assertHasNoErrors();

        $expense = GroupExpense::where('description', 'Dinner')->first();
        $this->assertNotNull($expense);

        // Check each share has exactly $30
        $this->assertDatabaseHas('group_expense_shares', [
            'group_expense_id' => $expense->id,
            'user_id' => $creator->id,
            'owed_amount' => 30.00,
        ]);
        $this->assertDatabaseHas('group_expense_shares', [
            'group_expense_id' => $expense->id,
            'user_id' => $user1->id,
            'owed_amount' => 30.00,
        ]);
        $this->assertDatabaseHas('group_expense_shares', [
            'group_expense_id' => $expense->id,
            'user_id' => $user2->id,
            'owed_amount' => 30.00,
        ]);
    }

    public function test_exact_split_validates_sum()
    {
        $creator = User::factory()->create(['is_admin' => false]);
        $group = Group::create(['name' => 'ExactTest', 'created_by' => $creator->id]);
        $user1 = User::factory()->create(['is_admin' => false]);
        
        GroupMember::create(['group_id' => $group->id, 'user_id' => $creator->id]);
        GroupMember::create(['group_id' => $group->id, 'user_id' => $user1->id]);

        $this->actingAs($creator);

        // Invalid exact amounts (sum is 45 + 50 = 95, but total amount is 100)
        Livewire::test(\App\Livewire\User\Groups\GroupDetail::class, ['id' => $group->id])
            ->set('description', 'Internet')
            ->set('amount', '100.00')
            ->set('splitMode', 'exact')
            ->set('paidBy', $creator->id)
            ->set('selectedMembers', [$creator->id, $user1->id])
            ->set('exactAmounts', [$creator->id => '45.00', $user1->id => '50.00'])
            ->call('saveExpense')
            ->assertHasErrors(['amount']);

        // Correct exact amounts (sum is 45 + 55 = 100)
        Livewire::test(\App\Livewire\User\Groups\GroupDetail::class, ['id' => $group->id])
            ->set('description', 'Internet')
            ->set('amount', '100.00')
            ->set('splitMode', 'exact')
            ->set('paidBy', $creator->id)
            ->set('selectedMembers', [$creator->id, $user1->id])
            ->set('exactAmounts', [$creator->id => '45.00', $user1->id => '55.00'])
            ->call('saveExpense')
            ->assertHasNoErrors();

        $expense = GroupExpense::where('description', 'Internet')->first();
        $this->assertNotNull($expense);
        $this->assertDatabaseHas('group_expense_shares', [
            'group_expense_id' => $expense->id,
            'user_id' => $creator->id,
            'owed_amount' => 45.00,
        ]);
        $this->assertDatabaseHas('group_expense_shares', [
            'group_expense_id' => $expense->id,
            'user_id' => $user1->id,
            'owed_amount' => 55.00,
        ]);
    }

    public function test_greedy_settlement_reconcile()
    {
        // 3 users: Alice, Bob, Charlie
        $alice = User::factory()->create(['is_admin' => false, 'name' => 'Alice']);
        $bob = User::factory()->create(['is_admin' => false, 'name' => 'Bob']);
        $charlie = User::factory()->create(['is_admin' => false, 'name' => 'Charlie']);

        $group = Group::create(['name' => 'SettlementTest', 'created_by' => $alice->id]);
        GroupMember::create(['group_id' => $group->id, 'user_id' => $alice->id]);
        GroupMember::create(['group_id' => $group->id, 'user_id' => $bob->id]);
        GroupMember::create(['group_id' => $group->id, 'user_id' => $charlie->id]);

        // Alice pays $90 split equally among all 3.
        // Each owes $30. Alice net: +$60. Bob net: -$30. Charlie net: -$30.
        $expense = GroupExpense::create([
            'group_id' => $group->id,
            'paid_by' => $alice->id,
            'description' => 'Dinner',
            'amount' => 90.00,
            'split_mode' => 'equal',
            'expense_date' => now(),
        ]);
        foreach ([$alice->id, $bob->id, $charlie->id] as $uid) {
            GroupExpenseShare::create([
                'group_expense_id' => $expense->id,
                'user_id' => $uid,
                'owed_amount' => 30.00,
            ]);
        }

        // Bob pays $30 split equally between Alice and Bob.
        // Each owes $15. Alice net: +60 - 15 = +$45. Bob net: -30 + 30 - 15 = -$15. Charlie net: -$30.
        $expense2 = GroupExpense::create([
            'group_id' => $group->id,
            'paid_by' => $bob->id,
            'description' => 'Drinks',
            'amount' => 30.00,
            'split_mode' => 'equal',
            'expense_date' => now(),
        ]);
        foreach ([$alice->id, $bob->id] as $uid) {
            GroupExpenseShare::create([
                'group_expense_id' => $expense2->id,
                'user_id' => $uid,
                'owed_amount' => 15.00,
            ]);
        }

        // Expected outstanding balances:
        // Alice: +$45 (creditor)
        // Bob: -$15 (debtor)
        // Charlie: -$30 (debtor)
        // Expected settlements instructions:
        // - Bob owes Alice $15
        // - Charlie owes Alice $30

        $this->actingAs($alice);
        $component = Livewire::test(\App\Livewire\User\Groups\GroupDetail::class, ['id' => $group->id]);
        
        $settlements = $component->instance()->settlements;
        $instructions = $settlements['instructions'];
        $balances = $settlements['balances'];

        $this->assertEquals(45.00, $balances[$alice->id]);
        $this->assertEquals(-15.00, $balances[$bob->id]);
        $this->assertEquals(-30.00, $balances[$charlie->id]);

        $this->assertCount(2, $instructions);
        
        // Assert that the instructions contain Charlie owing Alice $30 and Bob owing Alice $15
        $foundCharlie = false;
        $foundBob = false;
        foreach ($instructions as $inst) {
            if ($inst['debtor_id'] === $charlie->id && $inst['creditor_id'] === $alice->id) {
                $this->assertEquals(30.00, $inst['amount']);
                $foundCharlie = true;
            }
            if ($inst['debtor_id'] === $bob->id && $inst['creditor_id'] === $alice->id) {
                $this->assertEquals(15.00, $inst['amount']);
                $foundBob = true;
            }
        }
        $this->assertTrue($foundCharlie);
        $this->assertTrue($foundBob);
    }
}
