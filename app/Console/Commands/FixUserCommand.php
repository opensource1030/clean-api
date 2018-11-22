<?php
namespace WA\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use WA\DataStore\Role\Role;
use WA\DataStore\User\User;
use WA\DataStore\Company\Company;
use WA\DataStore\Company\CompanyDomains;

class FixUserCommand extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'wa:users:fix';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix user\'s company, domain and role.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Defines the arguments.
     *
     * @return array
     */
    public function getArguments()
    {
        return array(
            array('email', InputArgument::OPTIONAL, 'The e-mail of the user to fix.'),
        );
    }


    private function fixUser(User $user)
    {
        $email = $user->email;

        $errors = 0;
        if (count($user->roles()->get()) == 0) {
            $errors++;
            $userRole = Role::where('name', 'user')->first();
            if (isset($userRole)) {
                $user->roles()->sync([$userRole->id]);
            }
            $this->info('Role \'user\' set for user: '.$email);
        }

        if (count($user->companies()->get()) == 0) {
            $domain = CompanyDomains::where('domain', substr($email, strrpos($email, '@') + 1))->first();
            if (!$domain) {
                $this->error('Domain '.substr($email, strrpos($email, '@') + 1).' should exist for user: '.$email);
            } else {
                $errors++;
                $user->companyId = $domain->companyId;
                $user->save();
                $company = Company::find($domain->companyId);
                $this->info('Company set to '.$company->name.' ('.$domain->domain.') for user: '.$email.' Which had no company.');
            }
        }

        if (count($user->companies()->first()->domains()->get()) == 0) {
            $errors++;
            $domain = CompanyDomains::where('domain', substr($email, strrpos($email, '@') + 1))->first();
            if ($domain) {
                $user->companyId = $domain->companyId;
                $user->save();
                $company = Company::find($domain->companyId);
                $this->info('Fixed company set to '.$company->name.' ('.$domain->domain.') for user: '.$email);
            } else {
                $domain = new CompanyDomains();
                $domain->domain = substr($email, strrpos($email, '@') + 1);
                $domain->active = 1;
                $domain->companyId = $user->companyId;
                $domain->save();
                $this->info('Company Domain '. $domain->domain .' created for user: '.$email);
            }
        }

        if (substr($email, strrpos($email, '@') + 1) != $user->companies()->first()->domains()->first()->domain) {
            $domain = CompanyDomains::where('domain', substr($email, strrpos($email, '@') + 1))->first();
            if (!$domain) {
                $this->error('Domain '.substr($email, strrpos($email, '@') + 1).' should exist.');
            } else {
                $errors++;
                $user->companyId = $domain->companyId;
                $user->save();
                $company = Company::find($domain->companyId);
                $this->info('Company set to '.$company->name.' ('.$domain->domain.') for user: '.$email);
            }
        }

        if (!$errors) {
            $this->error('This user is already fixed!');
        } else {
            $this->info($email . ' fixed!');
        }
    }

    public function fixUsers($users) {
        foreach ($users as $user) {
            $this->fixUser($user);
        }
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $email = $this->argument('email');

        if ($email) {
            $this->line(sprintf('Fixing user: %s', $email));
            $user = User::where('email', $email)->first();
            if ($user) {
                $this->fixUser($user);
            } else {
                $this->error('User not found!');
            }
        } else {
            $this->line('Fix all users');

            User::doesntHave('roles')->chunkById(100, [$this, 'fixUsers']);

            User::doesntHave('companies')->chunkById(100, [$this, 'fixUsers']);

            $users = User::leftJoin('company_domains', 'users.companyId', '=', 'company_domains.companyId')->whereNull('company_domains.id')->get();
            $this->fixUsers($users);

            $users = User::join('company_domains', 'users.companyId', '=', 'company_domains.companyId')
                ->whereRaw('users.email NOT LIKE CONCAT(\'%@\', company_domains.domain)')
                ->get();
            $this->fixUsers($users);
        }

        $this->line('...done.');
    }
}

