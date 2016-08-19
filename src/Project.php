<?php namespace Scalex\Mailer;

class Project extends Eloquent\Model
{
    protected $fillable = ['name', 'directory'];

    public function tracks() {
        return $this->hasMany(Track::class);
    }
}
