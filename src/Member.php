<?php namespace Scalex\Mailer;

/**
 * Class Member
 * @package Scalex\Mailer
 * @property-read Track[]|\Illuminate\Database\Eloquent\Collection $tracks
 * @property string first_name
 * @property string last_name
 * @property string email
 */
class Member extends Eloquent\Model
{
    protected $fillable = ['email', 'first_name', 'last_name'];

    public function tracks() {
        return $this->hasMany(Track::class);
    }

    /**
     * @param $email
     * @param $list
     *
     * @return Member
     */
    static public function look($email, $list) {
        return static::where('email', $email)->where('mailing_list_id', $list)->first();
    }

    public function __toString() {
        return $this->first_name.' '.$this->last_name.' <'.$this->email.'>';
    }
}
