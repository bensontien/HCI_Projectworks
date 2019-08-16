package com.example.myapplication1;

import androidx.appcompat.app.AppCompatActivity;
import android.os.Handler;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;
import java.io.BufferedReader;
import java.io.BufferedWriter;
import java.io.IOError;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.OutputStreamWriter;
import java.net.InetAddress;
import java.net.Socket;

public class MainActivity extends AppCompatActivity {

    private Button btn_connect, btn_disConnect,btn_send;
    private TextView recieveAns;
    private EditText inputIP, inputMsg;
    private Socket socket;
    Thread connet_thread, recieve_thread, send_thread;
    BufferedReader bufferReader;
    BufferedWriter bufferWriter;
    String Msgsend, MsgRecceive;
    Handler handler;
    boolean AllThreadStop;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

        LinkXml();
        eventListener();
        objectInit();
    }

    void LinkXml(){

        btn_connect = findViewById(R.id.ui_btn_connect);
        btn_disConnect = findViewById(R.id.ui_btn_disConnect);
        btn_send = findViewById(R.id.ui_btn_msgsend);

        inputIP = findViewById(R.id.ui_txt_inputIP);
        inputMsg = findViewById(R.id.ui_txt_inputmsg);

        recieveAns = findViewById(R.id.recieve_ans_txt);
    }

    void eventListener(){

        btn_connect.setOnClickListener(btnClick_connect);
        btn_disConnect.setOnClickListener(btnClick_disconnect);
        btn_send.setOnClickListener(btnClick_send);
    }

    void objectInit()
    {
        connet_thread = null;
        recieve_thread = null;
        send_thread = null;
        bufferReader = null;
        bufferWriter = null;
        handler = new Handler();
        AllThreadStop = false;
    }

    void initConnet(){



            AllThreadStop = false;
            try {

                InetAddress severip = InetAddress.getByName(inputIP.getText().toString());
                socket = new Socket(severip,1390);
                bufferWriter = new BufferedWriter(new OutputStreamWriter(socket.getOutputStream()));
                bufferReader = new BufferedReader(new InputStreamReader(socket.getInputStream()));

                if(socket.isConnected()){
                    initData();
                    recieveAns.append("Connect!");
                    Log.d("Connect","Connect!");
                }else{

                    handler.post(runable_release);
                }

            } catch (IOException e) {
                e.printStackTrace();
            }



    }

    void initData(){

        if( recieve_thread == null){

            recieve_thread = new Thread(runable_recieve);
            recieve_thread.start();
        }

        if( send_thread == null){

            send_thread = new Thread(runable_send);
            send_thread.start();
        }


    }

    void realease(){

        if(socket != null && bufferWriter != null && bufferReader != null && connet_thread != null && recieve_thread != null && send_thread !=null){

            AllThreadStop = true;
            send_thread.interrupt();
            recieve_thread.interrupt();
            connet_thread.interrupt();

            try {

                bufferReader.close();
                bufferWriter.close();
                socket.close();

            } catch (IOException e) {
                e.printStackTrace();
            }

            socket = null;
            bufferWriter = null;
            bufferReader = null;
            connet_thread = null;
            send_thread = null;
            recieve_thread = null;

        }

    }

    Button.OnClickListener btnClick_connect = new View.OnClickListener() {
        @Override
        public void onClick(View view) {

            if( connet_thread == null){

                connet_thread = new Thread(runable_connect);

                connet_thread.start();

            }



        }
    };

    Button.OnClickListener btnClick_disconnect = new View.OnClickListener() {
        @Override
        public void onClick(View view) {

            realease();

        }
    };

    Button.OnClickListener btnClick_send = new View.OnClickListener() {
        @Override
        public void onClick(View view) {

            Msgsend = inputMsg.getText().toString();
            inputMsg.setText("");
        }
    };

    Runnable runable_connect = new Runnable() {
        @Override
        public void run() {

                initConnet();

        }
    };

    Runnable runable_recieve = new Runnable() {
        @Override
        public void run() {

            try {

                while (socket.isConnected()) {

                    if (AllThreadStop) {break;}

                    MsgRecceive = bufferReader.readLine();

                    if (MsgRecceive != null){

                        handler.post(UI_update);

                    }else{

                        handler.post(runable_release);

                    }

                }
            } catch (IOException e) {
                        e.printStackTrace();
            }


        }
    };

    Runnable runable_send = new Runnable() {
        @Override
        public void run() {

            try {

                while (socket.isConnected()){

                    if (AllThreadStop) {break;}

                    if(Msgsend != null){

                        bufferWriter.write(Msgsend+"\n");

                        bufferWriter.flush();

                        Msgsend = null;
                    }

                }


            } catch (IOException e) {
                e.printStackTrace();
            }

        }
    };

    Runnable runable_release = new Runnable() {
        @Override
        public void run() {

                realease();

        }
    };

    Runnable UI_update = new Runnable() {
        @Override
        public void run() {

            if (MsgRecceive != null){

                recieveAns.append(MsgRecceive);
                MsgRecceive = null;

            }

        }
    };
}
