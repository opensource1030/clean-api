<?php
namespace WA\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use WA\DataStore\User\User;

class CheckUserCommand extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'wa:users:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check user\'s company, domain and role.';

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
            array('email', InputArgument::OPTIONAL, 'The e-mail of the user to check.'),
        );
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
            $this->line(sprintf('Checking user: %s', $email));
            $user = User::where('email', $email)->first();
            if ($user) {
                $errors = 0;
                if (count($user->roles()->get()) == 0) {
                    $errors++;
                    $this->error('User has no role defined');
                }

                if (count($user->companies()->get()) == 0) {
                    $errors++;
                    $this->error('User has no company defined');
                } else if (count($user->companies()->first()->domains()->get()) == 0) {
                    $errors++;
                    $this->error('User company has no domain defined');
                } else if (substr($email, strrpos($email, '@') + 1) != $user->companies()->first()->domains()->first()->domain) {
                    $errors++;
                    $this->error('User email does not match domain');
                }

                if (!$errors) {
                    $this->info('This user looks good!');
                    $this->info('role: '.$user->roles()->first()->name);
                    $this->info('company: '.$user->companies()->first()->name);
                    $this->info('domain: '.$user->companies()->first()->domains()->first()->domain);
                }
            } else {
                $this->error('User not found!');
            }
        } else {
            $this->line('Check all users');

            $numUsersNoRoles = User::doesntHave('roles')->count();
            if($numUsersNoRoles) {
                $this->error($numUsersNoRoles.' users without a role defined.');
            }

            $numUsersNoCompanies = User::doesntHave('companies')->count();
            if($numUsersNoCompanies) {
                $this->error($numUsersNoCompanies.' users without a company defined.');
            }

            $numUsersNoDomains = User::leftJoin('company_domains', 'users.companyId', '=', 'company_domains.companyId')->whereNull('company_domains.id')->count();
            if($numUsersNoDomains) {
                $this->error($numUsersNoDomains.' users on companies without domains defined.');
            }

            $numUsersNoDomainMatch = User::join('company_domains', 'users.companyId', '=', 'company_domains.companyId')
                ->whereRaw('users.email NOT LIKE CONCAT(\'%@\', company_domains.domain)')->count();
            if($numUsersNoDomainMatch) {
                $this->error($numUsersNoDomainMatch.' users where email does not match the domain.');
            }

            /*
            foreach ($users as $user) {
                $this->line($user->email . ', ' . $user->companies()->first()->domains()->first()->domain);
            }
            */
        }

        $this->line('...done.');
    }
}

