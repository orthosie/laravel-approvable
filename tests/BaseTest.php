<?php

namespace Victorlap\Approvable\Tests;

use Victorlap\Approvable\Approval;
use Victorlap\Approvable\Tests\Models\UserCanApprove;
use Victorlap\Approvable\Tests\Models\UserCannotApprove;

class BaseTest extends TestCase
{
    public function testApproverCanCreate()
    {
        $user = $this->returnUserInstance(UserCanApprove::class);

        $user->save();

        $this->assertTrue($user->exists);
    }

    public function testRegularCanCreate()
    {
        $user = $this->returnUserInstance(UserCannotApprove::class);

        $user->save();

        $this->assertTrue($user->exists);
    }

    public function testApproverCanEdit()
    {
        $user = $this->returnUserInstance(UserCanApprove::class);
        $user->save();

        $user->name = 'Doe John';
        $user->save();

        $this->assertEquals('Doe John', $user->fresh()->name);
    }

    public function testRegularCannotEdit()
    {
        $user = $this->returnUserInstance(UserCannotApprove::class);
        $user->save();

        $user->name = 'Doe John';
        $user->save();

        $this->assertEquals('John Doe', $user->fresh()->name);
    }

    public function testRegularCannotEditNewAttribute()
    {
        $user = $this->returnUserInstance(UserCannotApprove::class);
        $user->save();

        $user->password = 'secret';
        $user->save();

        $this->assertEquals('', $user->fresh()->password);
    }

    public function testHasPendingModelChanges()
    {
        $user = $this->returnUserInstance(UserCannotApprove::class);
        $user->save();

        $this->assertFalse($user->isPendingApproval());

        $user->name = 'Doe John';
        $user->save();

        $this->assertTrue($user->isPendingApproval());
    }

    public function testHasPendingAttributeChanges()
    {
        $user = $this->returnUserInstance(UserCannotApprove::class);
        $user->save();

        $this->assertFalse($user->isPendingApproval('name'));

        $user->name = 'Doe John';
        $user->save();

        $this->assertTrue($user->isPendingApproval('name'));
    }

    public function testListPendingAttributes()
    {
        $user = $this->returnUserInstance(UserCannotApprove::class);
        $user->save();

        $this->assertEquals(collect(), $user->getPendingApprovalAttributes());

        $user->name = 'Doe John';
        $user->save();

        $this->assertEquals(collect('name'), $user->getPendingApprovalAttributes());
    }

    public function testClassApprovals()
    {
        $user = $this->returnUserInstance(UserCannotApprove::class);
        $user->save();

        $this->assertEquals(collect(), $user->classApprovals()->toBase());

        $user->name = 'Doe John';
        $user->save();

        $this->assertEquals(Approval::all(), $user->classApprovals());
    }
}
