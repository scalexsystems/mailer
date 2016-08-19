<?php namespace Scalex\Mailer;

class MailingList extends Eloquent\Model
{
    protected $fillable = ['name'];

    public function members() {
        return $this->hasMany(Member::class);
    }
}
