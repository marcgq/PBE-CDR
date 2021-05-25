package com.marcgq.coursemanager;

import androidx.appcompat.app.AppCompatActivity;

import android.content.Intent;
import android.os.AsyncTask;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.net.HttpURLConnection;
import java.net.MalformedURLException;
import java.net.ProtocolException;
import java.net.URL;
import java.util.concurrent.ExecutionException;

public class MainActivity extends AppCompatActivity {
    protected String ipAddress, username, password, name="";
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);
        Button btn = (Button) findViewById(R.id.loginButton);
        EditText edtTextAddress = (EditText) findViewById(R.id.editTextAddress);
        EditText edtTextUsername = (EditText) findViewById(R.id.editTextUsername);
        EditText edtTextPassword = (EditText) findViewById(R.id.editTextPassword);
        btn.setOnClickListener(new View.OnClickListener() {
            public void onClick(View v) {
                ipAddress = edtTextAddress.getText().toString();
                username = edtTextUsername.getText().toString();
                password = edtTextPassword.getText().toString();
                Log.i("info", ipAddress+username+password);
                try {
                    name = new GetUrlContentTask().execute(ipAddress+"/?students?uid="+password).get();
                } catch (ExecutionException | InterruptedException e) {
                    e.printStackTrace();
                }

                if (!name.equals("")){
                    Intent myIntent = new Intent(v.getContext(), QueryActivity.class);
                    myIntent.putExtra("name", name);
                    myIntent.putExtra("ipAddress", ipAddress);
                    startActivityForResult(myIntent, 0);
                }
            }
        });

    }
    private class GetUrlContentTask extends AsyncTask<String, Integer, String> {
        protected String doInBackground(String... urls) {
            URL url = null;
            try {
                url = new URL(urls[0]);
            } catch (MalformedURLException e) {
                e.printStackTrace();
            }
            HttpURLConnection connection = null;
            try {
                connection = (HttpURLConnection) url.openConnection();
            } catch (IOException e) {
                e.printStackTrace();
            }
            try {
                connection.setRequestMethod("GET");
            } catch (ProtocolException e) {
                e.printStackTrace();
            }
            connection.setDoOutput(true);
            connection.setConnectTimeout(5000);
            connection.setReadTimeout(5000);
            try {
                connection.connect();
            } catch (IOException e) {
                e.printStackTrace();
            }
            BufferedReader rd = null;
            try {
                rd = new BufferedReader(new InputStreamReader(connection.getInputStream()));
            } catch (IOException e) {
                e.printStackTrace();
            }
            String content = "", line = "";
            while (true) {
                try {
                    if (!((line = rd.readLine()) != null)) break;
                } catch (IOException e) {
                    e.printStackTrace();
                }
                content += line + "\n";
            }
            //return content;
            String nom = "";
            JSONArray json = null;
            try {
                json = new JSONArray(content);
            } catch (JSONException e) {
                e.printStackTrace();
            }
            try {
                JSONObject res = json.getJSONObject(0);
                nom = res.getString("name");

            } catch (JSONException e) {
                e.printStackTrace();
            }
            return nom;
        }

        protected void onProgressUpdate(Integer... progress) {
        }

        protected void onPostExecute(String result) {
        }
    }

}
