<?php namespace Scalex\Mailer;

/**
 * Class Member
 * @package Scalex\Mailer
 * @property-read Track[]|\Illuminate\Database\Eloquent\Collection $tracks
 * @property-read string name
 * @property string first_name
 * @property string last_name
 * @property string email
 * @property-read array address
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

    public function getAddressAttribute() {
        return [$this->email, $this->name];
    }

    public function getNameAttribute() {
        return trim($this->first_name.' '.$this->last_name);
    }

    public function __toString() {
        return $this->first_name.' '.$this->last_name.' <'.$this->email.'>';
    }
}
