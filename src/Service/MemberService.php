<?php 

class MemberService
{
    private $members = [];

    public function addMember($member)
    {
        $this->members[] = $member;
    }

    public function getMembers()
    {
        return $this->members;
    }
}