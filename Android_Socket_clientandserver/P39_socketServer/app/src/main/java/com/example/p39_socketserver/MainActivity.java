package com.example.p39_socketserver;

import android.net.wifi.WifiManager;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.text.format.Formatter;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.TextView;

import java.io.BufferedReader;
import java.io.BufferedWriter;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.OutputStreamWriter;
import java.net.ServerSocket;
import java.net.Socket;
import java.util.ArrayList;

public class MainActivity extends AppCompatActivity {

    private static int serverPort = 5050;
    private static ServerSocket serverSocket;
    private static int count = 0; // count the connection of client
    private static ArrayList<Socket> clients = new ArrayList<>();
    private boolean ServerOnRun = false;

    private Thread waitClientThread;

    private TextView serverIPTextView,serverMessageTextView;
    private Button startServerButton;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

        serverIPTextView = findViewById(R.id.serverIPTextView_ID);
        serverMessageTextView = findViewById(R.id.serverMessageTextView_ID);
        startServerButton = findViewById(R.id.startServerButton_ID);

        WifiManager wm = (WifiManager) getApplicationContext().getSystemService(WIFI_SERVICE);
        String ip = Formatter.formatIpAddress(wm.getConnectionInfo().getIpAddress());
        serverIPTextView.setText(ip);

        startServerButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                if(!ServerOnRun)
                {
                    try{
                        serverSocket = new ServerSocket(serverPort);
                        serverMessageTextView.append("server is start.\n");
                        serverMessageTextView.append("Waiting for client connection\n");

                        waitClientThread = new Thread(new Runnable() {
                            @Override
                            public void run() {
                                while(!serverSocket.isClosed())
                                {
                                    waitNewClient();
                                }
                            }
                        });
                        waitClientThread.start();
                        startServerButton.setText(getResources().getString(R.string.stop_server));
                        ServerOnRun = true;
                    }catch (IOException e)
                    {
                        serverMessageTextView.append("Server socket Error\n");
                    }
                }
                else
                {
                    Thread shutDownThread = new Thread(new Runnable() {
                        @Override
                        public void run() {
                            castMsg("Server shutDown",true);
                            runOnUiThread(new Runnable() {
                                @Override
                                public void run() {
                                    startServerButton.setText(getResources().getString(R.string.start_server));
                                }
                            });
                            ServerOnRun = false;
                        }
                    });
                    shutDownThread.start();
                }

            }
        });

    }

    private void waitNewClient()
    {
        try{
            Socket socket = serverSocket.accept();
            count++;
            serverMessageTextView.append("現在使用者個數：" + count + "\n");

            addNewClient(socket);
        }catch (IOException e){}
    }

    private void addNewClient(final Socket socket){
        Thread thread = new Thread(new Runnable() {
            @Override
            public void run() {
                try{
                    clients.add(socket);
                    BufferedReader bufferedReader = new BufferedReader(new InputStreamReader(socket.getInputStream()));

                    while(socket.isConnected()){
                        String msg = bufferedReader.readLine();

                        if(msg == null)
                        {
                            serverMessageTextView.append("Client Disconnected\n");
                            break;
                        }
                        serverMessageTextView.append(msg+"\n");
                        castMsg(msg,false);
                    }
                }catch (IOException e)
                {
                    e.getStackTrace();
                }finally {
                    clients.remove(socket);
                    count--;
                    serverMessageTextView.append("現在使用者個數：" + count + "\n");
                }
            }
        });
        thread.start();
    }

    private void castMsg(String Msg,boolean toClose){
        Socket[] clientArrays = new Socket[clients.size()];
        clients.toArray(clientArrays);
        for(Socket socket:clientArrays){
            try{
                BufferedWriter bufferedWriter;
                bufferedWriter = new BufferedWriter(new OutputStreamWriter(socket.getOutputStream()));
                bufferedWriter.write(Msg+"\n");
                bufferedWriter.flush();
            }catch (IOException e){}
        }
        if(toClose){
            try{
                for(int i=0;i<clients.size();i++)
                {
                    clients.get(i).close();
                }
                clients.clear();
                serverSocket.close();
            }catch (IOException e)
            {
                Log.e("Close Server Error",e.toString());
            }

        }
    }

}
