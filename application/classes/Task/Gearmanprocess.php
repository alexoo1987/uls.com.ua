<?php defined('SYSPATH') or die('No direct script access.');
class Task_Gearmanprocess extends Minion_Task {
    protected function _execute(array $params)
    {
        Minion_CLI::write('Start worker');
        $this->action_gearmanworker();
    }

    public function action_gearmanworker(){
        $worker= new GearmanWorker();
        $worker->addServer();

        $worker->addFunction("processfile", array($this, "process_file"));
        while ($worker->work());
    }

    public function process_file($job)
    {
        Minion_CLI::write(date("Y-m-d H:i:s") ." Started new job");
        $sess_data = $job->workload();
        Minion_CLI::write("Sess_data=\"". $sess_data ."\"");
        //exec('php index.php processfile --sess_data="'.base64_encode($sess_data).'" &');
        $command = 'php /var/www/ulc.com.ua/index.php processfile --sess_data="'.base64_encode($sess_data).'"';
        Minion_CLI::write("Try to start $command");
        exec($command, $arrOut, $ret_val);
        Minion_CLI::write("Output is:");
        foreach($arrOut as $str) Minion_CLI::write($str);
        Minion_CLI::write("Return code is $ret_val");
        //Minion_Task::factory(array('task' => 'processfile'))->set_options(array('sess_data' => $sess_data))->execute();
	Minion_CLI::write(date("Y-m-d H:i:s") .' Task ended');
    }
}
?>
