package com.example.p39_socketclient;

import android.app.ActionBar;
import android.content.DialogInterface;
import android.os.Handler;
import android.support.v7.app.AlertDialog;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.util.DisplayMetrics;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.Window;
import android.view.WindowManager;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;
import android.widget.Toast;

import org.json.JSONObject;

import java.io.BufferedReader;
import java.io.BufferedWriter;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.OutputStreamWriter;
import java.net.InetAddress;
import java.net.Socket;

public class MainActivity extends AppCompatActivity {

    //GUI、Socket、thread的配置
    private EditText messageEditText,userEditText,IPAddressEditText;
    private TextView messageBlockTextView;
    private Button sendButton;
    private String tmp;
    private Socket clientSocket;
    public static Handler handler = new Handler();
    private String userName,IPAddress;
    private AlertDialog userNameAlertDialog;

    private BufferedWriter bufferedWriter;
    private BufferedReader bufferedReader;
    private Thread thread;
    private JSONObject jsonWrite,jsonRead;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

        //連結GUI
        messageEditText = findViewById(R.id.messageEditText_ID);
        messageBlockTextView = findViewById(R.id.messageBlockTextView_ID);
        sendButton = findViewById(R.id.sendButton_ID);
        setSendButton();

        AlertDialog.Builder builder = new AlertDialog.Builder(MainActivity.this);

        LayoutInflater layoutInflater = LayoutInflater.from(this);
        View view = layoutInflater.inflate(R.layout.log_in_dialog,null);
        builder.setView(view);
        builder.setTitle("Initial data");

        //輸入User ID以及伺服器IP
        userEditText = view.findViewById(R.id.userEditText_ID);
        IPAddressEditText = view.findViewById(R.id.IPAddressEditText_ID);

        builder.setPositiveButton("確認",null);
        userNameAlertDialog = builder.create();

        //輸入完資訊後按下按鈕就開始連線
        userNameAlertDialog.setOnShowListener(new DialogInterface.OnShowListener() {
            @Override
            public void onShow(DialogInterface dialog) {
                Button positiveButton = userNameAlertDialog.getButton(AlertDialog.BUTTON_POSITIVE);
                positiveButton.setOnClickListener(new View.OnClickListener() {
                    @Override
                    public void onClick(View v) {
                        if(userEditText.getText().toString().length() ==0 || IPAddressEditText.getText().toString().length() == 0)
                        {
                            Toast.makeText(MainActivity.this,"輸入資訊不完整",Toast.LENGTH_LONG).show();
                        }
                        else
                        {
                            userName = userEditText.getText().toString();
                            IPAddress = IPAddressEditText.getText().toString();
                            userNameAlertDialog.cancel();
                            thread = new Thread(readData);// new 一個 readData 的thread 也就是開始連線
                            thread.start();
                        }
                    }
                });
            }
        });
        userNameAlertDialog.show();
        userNameAlertDialog.setCancelable(false);

        WindowManager.LayoutParams layoutParams;
        Window window = userNameAlertDialog.getWindow();
        DisplayMetrics displayMetrics = new DisplayMetrics();
        getWindowManager().getDefaultDisplay().getMetrics(displayMetrics);
        layoutParams = window.getAttributes();
        layoutParams.width = (int) (displayMetrics.widthPixels * 0.75);
        userNameAlertDialog.getWindow().setAttributes(layoutParams);


    }

    //這裡是傳要給Server的東西
    private void setSendButton()
    {
        sendButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Thread sendThread = new Thread(new Runnable() {
                    @Override
                    public void run() {
                        try{
                            //取得網路輸出串流
                            bufferedWriter = new BufferedWriter(new OutputStreamWriter(clientSocket.getOutputStream()));

                            //寫入訊息
                            bufferedWriter.write(userName+ ":" + messageEditText.getText().toString() + "\n");

                            bufferedWriter.flush();
                        }catch (IOException e)
                        {
                            Log.e("sendMessageError",e.toString());
                        }
                        runOnUiThread(new Runnable() {
                            @Override
                            public void run() {
                                messageEditText.setText("");
                            }
                        });
                    }
                });
                sendThread.start();

            }
        });
    }

    //GUI的/runnable
    private Runnable updateText = new Runnable() {
        @Override
        public void run() {
            messageBlockTextView.append(tmp + "\n");
        }
    };

    //runnable readData 給剛剛上面開的thread 跑的
    private Runnable readData = new Runnable() {
        @Override
        public void run() {
            try{
                //設定server端IP
                InetAddress serverIP = InetAddress.getByName(IPAddress);
                //自訂port
                int serverPort = 5050;
                //建立連線
                clientSocket = new Socket(serverIP,serverPort);
                //取得網路輸出串流
                bufferedWriter = new BufferedWriter(new OutputStreamWriter(clientSocket.getOutputStream()));
                //取得網路輸入串流
                bufferedReader = new BufferedReader(new InputStreamReader(clientSocket.getInputStream()));

                //檢查是否已經連線
                while(clientSocket.isConnected()){
                    //宣告一個緩衝，從bufferReader串流讀取Server端傳來的訊息
                    tmp = bufferedReader.readLine();

                    if(tmp!=null)
                    {
                        handler.post(updateText);
                    }
                }
            }catch (Exception e)
            {
                e.printStackTrace();
                Log.e("text","Socket連線 = "+e.toString());
                finish();
            }

        }
    };

    @Override
    protected void onDestroy() {
        super.onDestroy();
        Thread closeThread = new Thread(new Runnable() {
            @Override
            public void run() {
                try{
                    //傳送離線 Action 給 Server 端
                    jsonWrite = new JSONObject();
                    jsonWrite.put("action",userName + "已離線");

                    //寫入
                    bufferedWriter.write(jsonWrite + "\n");
                    //立即發送
                    bufferedWriter.flush();

                    //關閉輸出串流後，關閉Socket
                    bufferedWriter.close();
                    bufferedReader.close();
                    clientSocket.close();
                }catch (Exception e)
                {
                    e.printStackTrace();
                    Log.e("Error","Socket close Error");
                }
            }
        });
        closeThread.start();

    }
}
